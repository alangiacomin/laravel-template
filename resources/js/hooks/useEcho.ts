import {useCallback, useEffect, useMemo} from "react";
import {configureEcho, echo as echoLaravel} from "@laravel/echo-react";

type useChannelType = {
    listen: (eventName: string, callback: CallableFunction) => () => void
};

configureEcho({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// console.log("ECHO definition");
const echo = echoLaravel();

const useChannel = (channelName: string, type: "public" | "private") => {
    const channel = useMemo(() => {
        if (!channelName) return undefined;
        // console.log("ECHO channel", channelName)
        return type === 'public' ? echo.channel(channelName) : echo.private(channelName);
    }, [channelName, type]);

    const listen = useCallback((eventName: string, callback: CallableFunction) => {
        if (!channel) return () => {
        };
        const formatted = "." + eventName;
        // console.log("ECHO listen", formatted);
        channel.listen(formatted, callback);
        return () => {
            // console.log("ECHO stopListening", formatted);
            channel.stopListening(formatted);
        };
    }, [channel]);

    useEffect(() => {
        return () => {
            if (!channelName) return;
            // console.log("ECHO leave", channelName);
            echo.leave(channelName);
        };
    }, [channelName]);

    return {listen};
};

const usePublicChannel = (channelName: string): useChannelType => {
    return useChannel(channelName, 'public')
};

const usePrivateChannel = (userId?: number): useChannelType => {
    return useChannel(userId ? `App.Models.User.${userId}` : '', 'private');
};

export {
    usePublicChannel,
    usePrivateChannel,
};
