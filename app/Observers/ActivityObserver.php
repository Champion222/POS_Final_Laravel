<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityObserver
{
    public function created(Model $model): void
    {
        $this->log(
            event: 'created',
            model: $model,
            properties: [
                'new' => $this->sanitizeAttributes($model->getAttributes()),
            ]
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at'], $changes['created_at']);

        if ($changes === []) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $this->log(
            event: $this->resolveUpdatedEvent($model, array_keys($changes)),
            model: $model,
            properties: [
                'old' => $this->sanitizeAttributes($oldValues),
                'new' => $this->sanitizeAttributes($changes),
            ]
        );
    }

    public function deleted(Model $model): void
    {
        $this->log(
            event: 'deleted',
            model: $model,
            properties: [
                'old' => $this->sanitizeAttributes($model->getOriginal()),
            ]
        );
    }

    /**
     * @param  array{old?:array<string, mixed>, new?:array<string, mixed>}  $properties
     */
    private function log(string $event, Model $model, array $properties): void
    {
        $userId = Auth::id();
        if ($userId === null || $model instanceof ActivityLog) {
            return;
        }

        $request = request();

        ActivityLog::query()->create([
            'user_id' => $userId,
            'event' => $event,
            'description' => $this->descriptionFor($event, $model),
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'subject_label' => $this->subjectLabel($model),
            'method' => $request?->method(),
            'route_name' => $request?->route()?->getName(),
            'url' => $request?->fullUrl(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'properties' => $properties,
        ]);
    }

    /**
     * @param  list<string>  $changedKeys
     */
    private function resolveUpdatedEvent(Model $model, array $changedKeys): string
    {
        if ($model instanceof User) {
            if (in_array('password', $changedKeys, true)) {
                return 'password_updated';
            }

            if (array_intersect($changedKeys, ['name', 'email', 'image'])) {
                return 'profile_updated';
            }
        }

        if ($model instanceof Product && in_array('qty', $changedKeys, true)) {
            return 'stock_updated';
        }

        return 'updated';
    }

    private function descriptionFor(string $event, Model $model): string
    {
        $modelLabel = Str::headline(class_basename($model));

        if ($event === 'password_updated' && $model instanceof User) {
            $actorId = Auth::id();
            if ($actorId !== null && $actorId !== (int) $model->getKey()) {
                return 'Updated user password';
            }

            return 'Updated account password';
        }

        return match ($event) {
            'created' => "Created {$modelLabel}",
            'deleted' => "Deleted {$modelLabel}",
            'profile_updated' => 'Updated account profile',
            'stock_updated' => 'Adjusted product stock',
            default => "Updated {$modelLabel}",
        };
    }

    private function subjectLabel(Model $model): string
    {
        $attributes = $model->getAttributes();

        foreach (['name', 'title', 'invoice_number', 'email'] as $key) {
            if (! empty($attributes[$key])) {
                return (string) $attributes[$key];
            }
        }

        return (string) $model->getKey();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function sanitizeAttributes(array $attributes): array
    {
        $sanitized = [];

        foreach ($attributes as $key => $value) {
            $keyString = Str::lower((string) $key);

            if (Str::contains($keyString, ['password', 'token'])) {
                $sanitized[$key] = '[hidden]';

                continue;
            }

            $sanitized[$key] = $this->normalizeValue($value);
        }

        return $sanitized;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeValue($item);
            }

            return $normalized;
        }

        if (is_bool($value) || is_numeric($value) || $value === null) {
            return $value;
        }

        if (is_object($value)) {
            return '[object]';
        }

        $stringValue = (string) $value;
        if (mb_strlen($stringValue) > 500) {
            return mb_substr($stringValue, 0, 500).'...';
        }

        return $stringValue;
    }
}
