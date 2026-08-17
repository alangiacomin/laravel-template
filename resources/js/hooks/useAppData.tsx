type AppDataType = {
    appName: string;
}

const useAppData = (): AppDataType => {
    return {
        appName: import.meta.env.VITE_APP_NAME,
    }
}

export default useAppData;
