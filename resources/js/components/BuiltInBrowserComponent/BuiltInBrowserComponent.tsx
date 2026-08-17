import {FC, ReactNode} from "react";
import EnvVars from "../../hooks/useAppData.tsx";

type BuiltInBrowserComponentProps = {
    title?: string;
}
const BuiltInBrowserComponent: FC<BuiltInBrowserComponentProps> = ({title}: BuiltInBrowserComponentProps): ReactNode => {

    const titleTokens = [
        EnvVars.appName,
        title
    ];
    return (
        <title>
            {titleTokens.filter(x => x).join(" - ")}
        </title>
    );
}

export default BuiltInBrowserComponent;
