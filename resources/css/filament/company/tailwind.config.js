import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/**/*.php',
        './resources/views/filament/admin/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php', 
    ],
    plugins: [require('@tailwindcss/forms'),require('tailwindcss-animate')],
    theme: {
      extend: {
        colors: {
          brown: {
            100: '#f3e5e0',
            200: '#e6ccb3',
            300: '#d9b38c',
            400: '#cc9966',
            500: '#b36b33',
            600: '#99592b',
            700: '#804723',
            800: '#66361a',
            900: '#4d2612',
          },
        },
      },
    },
}

 