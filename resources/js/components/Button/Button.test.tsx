import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import Button from "./Button.tsx";

describe("Button", () => {
  it("renderizza figli, classe btn e type=button di default", () => {
    render(<Button className="btn-primary">Salva</Button>);

    const button = screen.getByRole("button", { name: "Salva" });
    expect(button).toHaveClass("btn", "btn-primary");
    expect(button).toHaveAttribute("type", "button");
  });

  it("invoca onClick", async () => {
    const onClick = vi.fn();
    render(<Button onClick={onClick}>Vai</Button>);

    await userEvent.click(screen.getByRole("button", { name: "Vai" }));

    expect(onClick).toHaveBeenCalledOnce();
  });

  it("non invoca onClick se disabilitato", async () => {
    const onClick = vi.fn();
    render(
      <Button onClick={onClick} disabled>
        Vai
      </Button>,
    );

    await userEvent.click(screen.getByRole("button", { name: "Vai" }));

    expect(onClick).not.toHaveBeenCalled();
  });
});
