import type { FC, ReactNode } from "react";
import useAppData from "../../hooks/useAppData.tsx";

type BuiltInBrowserComponentProps = {
  title?: string;
};
const BuiltInBrowserComponent: FC<BuiltInBrowserComponentProps> = ({
  title,
}: BuiltInBrowserComponentProps): ReactNode => {
  const { appName } = useAppData();
  const titleTokens = [appName, title];
  return <title>{titleTokens.filter((x) => x).join(" - ")}</title>;
};

export default BuiltInBrowserComponent;
