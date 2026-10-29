import { renderHook } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { useRoutes } from "./useRoutes.ts";

const usePageMock = vi.hoisted(() => vi.fn());

vi.mock("@inertiajs/react", () => ({ usePage: usePageMock }));

const renderRoutes = (url: string) => {
  usePageMock.mockReturnValue({ url });

  return renderHook(() => useRoutes()).result.current;
};

describe("useRoutes", () => {
  it("costruisce le route tramite route()", () => {
    const routes = renderRoutes("/");

    expect(routes.app.login()).toBe("http://localhost/login");
    expect(routes.admin.user(7)).toBe("http://localhost/admin.user.show?id=7");
    expect(routes.admin.role(3)).toBe("http://localhost/admin.role.show?id=3");
  });

  it("isActive: match esatto", () => {
    const { isActive } = renderRoutes("/admin.users");

    expect(isActive("/admin.users")).toBe(true);
    expect(isActive("/admin.roles")).toBe(false);
  });

  it("isActive: match parziale sui sotto-percorsi", () => {
    const { isActive } = renderRoutes("/admin.users/5");

    expect(isActive("/admin.users")).toBe(true);
  });

  it("isActive: le radici non fanno match parziale", () => {
    const { isActive } = renderRoutes("/login");

    expect(isActive("http://localhost/home")).toBe(false);
  });
});
