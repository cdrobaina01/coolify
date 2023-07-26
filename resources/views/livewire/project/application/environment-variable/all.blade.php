<div class="flex flex-col gap-2">
    <div>
        <div class="flex items-center gap-2">
            <h2>Environment Variables</h2>
            <x-forms.button class="btn" onclick="newVariable.showModal()">+ Add</x-forms.button>
            <livewire:project.application.environment-variable.add />
        </div>
        <div class="">Environment (secrets) variables for normal deployments.</div>
    </div>
    @forelse ($application->environment_variables as $env)
        <livewire:project.application.environment-variable.show wire:key="environment-{{ $env->id }}"
            :env="$env" />
    @empty
        <div class="text-neutral-500">No environment variables found.</div>
    @endforelse
    @if ($application->environment_variables_preview->count() > 0)
        <div>
            <h3>Preview Deployments</h3>
            <div class="">Environment (secrets) variables for Preview Deployments.</div>
        </div>
        @foreach ($application->environment_variables_preview as $env)
            <livewire:project.application.environment-variable.show wire:key="environment-{{ $env->id }}"
                :env="$env" />
        @endforeach
    @endif
</div>