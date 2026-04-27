<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" color="primary">
                <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                Save Settings
            </x-filament::button>

            <x-filament::button 
                type="button" 
                color="success" 
                wire:click="$dispatch('test-toast')"
                class="ml-2">
                <x-heroicon-o-bell-alert class="w-5 h-5 mr-2" />
                Test Toast
            </x-filament::button>
        </div>
    </form>

    {{-- Preview Section --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Preview
        </x-slot>
        <x-slot name="description">
            Click "Test Toast" to see how your notifications will look
        </x-slot>

        <div class="space-y-3">
            <div class="flex items-center space-x-3">
                <x-filament::button 
                    size="sm" 
                    color="success"
                    onclick="if(window.ToastNotification) ToastNotification.success('This is a success message!')">
                    Success Example
                </x-filament::button>

                <x-filament::button 
                    size="sm" 
                    color="danger"
                    onclick="if(window.ToastNotification) ToastNotification.error('This is an error message!')">
                    Error Example
                </x-filament::button>

                <x-filament::button 
                    size="sm" 
                    color="warning"
                    onclick="if(window.ToastNotification) ToastNotification.warning('This is a warning message!')">
                    Warning Example
                </x-filament::button>

                <x-filament::button 
                    size="sm" 
                    color="info"
                    onclick="if(window.ToastNotification) ToastNotification.info('This is an info message!')">
                    Info Example
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
