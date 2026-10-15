import { renderHook } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import useTranslations from "./useTranslations.tsx";

const usePageMock = vi.hoisted(() => vi.fn());

vi.mock("@inertiajs/react", () => ({ usePage: usePageMock }));

const withTranslations = (
  translations?: Record<string, Record<string, string>>,
) => usePageMock.mockReturnValue({ props: { translations } });

describe("useTranslations", () => {
  it("risolve le chiavi con sezione esplicita", () => {
    withTranslations({ admin: { users: "Utenti" } });
    const { result } = renderHook(() => useTranslations());

    expect(result.current("admin.users")).toBe("Utenti");
  });

  it("usa la sezione global se la chiave non ha sezione", () => {
    withTranslations({ global: { home: "Home" } });
    const { result } = renderHook(() => useTranslations());

    expect(result.current("home")).toBe("Home");
  });

  it("sostituisce i placeholder", () => {
    withTranslations({ global: { hello: "Ciao :name" } });
    const { result } = renderHook(() => useTranslations());

    expect(result.current("hello", { name: "Mario" })).toBe("Ciao Mario");
  });

  it("restituisce la chiave tra parentesi se la traduzione manca", () => {
    withTranslations({ admin: {} });
    const { result } = renderHook(() => useTranslations());

    expect(result.current("admin.missing")).toBe("[admin.missing]");
    expect(result.current("missing")).toBe("[global.missing]");
  });

  it("gestisce l'assenza di traduzioni", () => {
    withTranslations(undefined);
    const { result } = renderHook(() => useTranslations());

    expect(result.current("admin.users")).toBe("[admin.users]");
  });
});
