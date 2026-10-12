import type { FC, ReactNode } from "react";
import useTranslations from "../../../hooks/useTranslations.tsx";
import Page from "../components/Page/Page.tsx";
import Header from "./Header.tsx";
import "./home.css";
import useAuth from "../../../hooks/useAuth.tsx";
import CallToAction from "./CallToAction.tsx";
import Promozione from "./Promozione.tsx";

const Home: FC = (): ReactNode => {
  const user = useAuth();
  const __ = useTranslations();

  return (
    <Page browserTitle={__("home.browser_title")} className={"home"}>
      <Header />
      <Promozione />
      {!user && <CallToAction />}
    </Page>
  );
};

export default Home;
