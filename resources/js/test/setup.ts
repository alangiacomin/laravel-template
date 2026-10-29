import "@testing-library/jest-dom/vitest";
import { cleanup } from "@testing-library/react";
import { afterEach, vi } from "vitest";

afterEach(() => {
  cleanup();
});

/**
 * Ziggy `route()` e' globale nell'app: nei test viene sostituito con una versione
 * minimale che produce `http://localhost/<nome>?<parametri>`.
 */
vi.stubGlobal(
  "route",
  (name: string, params?: Record<string, string | number>): string => {
    const query = params
      ? `?${new URLSearchParams(Object.entries(params).map(([k, v]) => [k, String(v)]))}`
      : "";

    return `http://localhost/${name}${query}`;
  },
);
