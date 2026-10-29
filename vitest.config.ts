import react from "@vitejs/plugin-react";
import { defineConfig } from "vitest/config";

export default defineConfig({
  plugins: [react()],
  test: {
    environment: "happy-dom",
    setupFiles: ["resources/js/test/setup.ts"],
    include: ["resources/js/**/*.test.{ts,tsx}"],
    restoreMocks: true,
  },
});
