/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Los colores exactos basados en image_d8f55c.png
        'eco-green': '#1E5E3A',       // Verde pino del sidebar y navbar superior
        'eco-sidebar': '#3B5944',     // Fondo del menú izquierdo
        'eco-bg': '#F4F6F8',          // Fondo gris muy claro de la sección principal
        'eco-active-menu': '#53745D', // Color de fondo del botón activo (Productos)
      }
    },
  },
  plugins: [],
}