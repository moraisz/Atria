import { defineConfig } from "vite";
import atriaPhp from "./vendor/moraisz/atria-core/resources/vite/atriaPhpPlugin.js";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    tailwindcss(),
    atriaPhp({
      input: [
        "app/Views/static/main.js",
        "app/Views/static/style.css",
      ],
    }),
  ],
});
