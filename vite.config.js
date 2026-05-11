import { defineConfig } from "vite";

export default defineConfig({
  base: "/assets/",
  build: {
    outDir: "public/assets",
    assetsDir: "",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: "resources/js/app.js",
        users: "resources/js/users.js",
      },
    },
    manifest: true,
  },
});
