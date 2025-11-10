module.exports = {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          500: '#0ea5e9',
          600: '#0284c7',
          900: '#0c4a6e',
        },
        secondary: {
          50: '#fafafa',
          100: '#f4f4f5',
          500: '#71717a',
          600: '#52525b',
          900: '#18181b',
        },
        // Jouw custom theme colors toevoegen
        custom: {
          bg: {
            primary: '#ffffff',
            secondary: '#f8fafc',
            card: '#ffffff',
          },
          text: {
            primary: '#1e293b',
            secondary: '#64748b',
          },
          border: '#e2e8f0',
          accent: '#0ea5e9',
        }
      },
      backgroundColor: {
        'glass': 'rgba(255, 255, 255, 0.1)',
        'glass-dark': 'rgba(42, 45, 62, 0.8)',
      },
      backdropBlur: {
        'glass': '10px',
      },
      borderColor: {
        'glass': 'rgba(255, 255, 255, 0.1)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        }
      }
    },
  },
  plugins: [],
}