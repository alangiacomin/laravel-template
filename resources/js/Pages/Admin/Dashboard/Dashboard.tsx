import {FC, ReactNode} from "react";
import Page from "../../Admin/components/Page/Page.tsx";
import {usePage} from "@inertiajs/react";
import UsersCard from "./UsersCard.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";

type DashboardPageProps = {
    dashboard: {
        users_total_count: number;
        users_total_count_unverified: number;
        users_month_count: number;
        users_month_count_unverified: number;
        users_last_month_count: number;
        users_last_month_count_unverified: number;
    }
}

const Dashboard: FC = (): ReactNode => {

    const {dashboard} = usePage<DashboardPageProps>().props;
    const {users_total_count, users_total_count_unverified} = dashboard;
    const {users_month_count, users_month_count_unverified} = dashboard;
    const {users_last_month_count, users_last_month_count_unverified} = dashboard;
    const __ = useTranslations();

    return (
        <Page
            title={__('admin.dashboard')}
            subtitle={__('admin.dashboard_subtitle')}
            browserTitle={__('admin.dashboard')}>


            <div className="row g-4">

                <div className="col-md-4">
                    <UsersCard
                        title={__('admin.users_total')}
                        subtitle={__('admin.users_total_subtitle')}
                        count={users_total_count}
                        unverifiedCount={users_total_count_unverified}
                    />
                </div>

                <div className="col-md-4">
                    <UsersCard
                        title={__('admin.users_this_month')}
                        subtitle={__('admin.users_this_month_subtitle')}
                        count={users_month_count}
                        unverifiedCount={users_month_count_unverified}
                    />
                </div>

                <div className="col-md-4">
                    <UsersCard
                        title={__('admin.users_last_month')}
                        subtitle={__('admin.users_last_month_subtitle')}
                        count={users_last_month_count}
                        unverifiedCount={users_last_month_count_unverified}
                    />
                </div>

            </div>
        </Page>
    );
}

export default Dashboard;
