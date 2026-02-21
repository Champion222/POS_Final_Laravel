<x-app-layout>
    @section('header', 'Create Promotion')

    <div class="max-w-7xl mx-auto">
        <form method="POST" action="{{ route('promotions.store') }}" class="space-y-8">
            @csrf
            @include('promotions.partials.form', [
                'promotion' => null,
                'selectedProducts' => [],
                'submitLabel' => 'Create Promotion',
            ])
        </form>
    </div>
</x-app-layout>
