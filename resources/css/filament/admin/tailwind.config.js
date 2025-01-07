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
            blue: {
              100: '#ebf8ff',  // Lightest blue
              200: '#bee3f8',  // Light blue
              300: '#90cdf4',  // Soft blue
              400: '#63b3ed',  // Sky blue
              500: '#4299e1',  // Primary blue
              600: '#3182ce',  // Medium blue
              700: '#2b6cb0',  // Dark blue
              800: '#2c5282',  // Deeper blue
              900: '#2a4365',  // Deepest blue
            },
          },
        },
      }
      ,
}

 