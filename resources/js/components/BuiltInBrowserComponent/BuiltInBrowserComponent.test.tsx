import { render, waitFor } from "@testing-library/react";
import { afterEach, describe, expect, it, vi } from "vitest";
import BuiltInBrowserComponent from "./BuiltInBrowserComponent.tsx";

describe("BuiltInBrowserComponent", () => {
  afterEach(() => {
    vi.unstubAllEnvs();
  });

  it("compone nome app e titolo", async () => {
    vi.stubEnv("VITE_APP_NAME", "Template");
    render(<BuiltInBrowserComponent title="Utenti" />);

    await waitFor(() => expect(document.title).toBe("Template - Utenti"));
  });

  it("omette il titolo se assente", async () => {
    vi.stubEnv("VITE_APP_NAME", "Template");
    render(<BuiltInBrowserComponent />);

    await waitFor(() => expect(document.title).toBe("Template"));
  });
});
