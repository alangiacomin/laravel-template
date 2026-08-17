import {FC, ReactNode} from "react";

const HomeIcon: FC = (): ReactNode => {

    return (
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
             className="bi bi-house-door me-2" viewBox="0 0 16 16">
            <path
                d="M8.354 1.146a.5.5 0 0 0-.708 0L1 7.793V14.5A1.5 1.5 0 0 0 2.5 16h3A1.5 1.5 0 0 0 7 14.5V11h2v3.5A1.5 1.5 0 0 0 10.5 16h3a1.5 1.5 0 0 0 1.5-1.5V7.793l-6.146-6.647zM2 13V7.707l6-6 6 6V13a.5.5 0 0 1-.5.5H10v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v3H2.5A.5.5 0 0 1 2 13z"/>
        </svg>
    );
}

export default HomeIcon;
