<x-app-layout>
    @section('header', 'Edit Promotion')

    <div class="max-w-7xl mx-auto">
        <form method="POST" action="{{ route('promotions.update', $promotion) }}" class="space-y-8">
            @csrf
            @method('PUT')
            @include('promotions.partials.form', [
                'promotion' => $promotion,
                'selectedProducts' => $selectedProducts ?? [],
                'submitLabel' => 'Update Promotion',
            ])
        </form>
    </div>
</x-app-layout>
