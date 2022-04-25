import React, {createContext, useState} from "react";

export const AppContext = createContext(undefined, undefined);

const AppContextProvider = (props) => {
    const [appToken, setAppToken] = useState({success: false});
    const [userRole, setUserRole] = useState(false);

    return (
        <AppContext.Provider value={{
            appToken,
            setAppToken,
            userRole,
            setUserRole
        }}>
            {props.children}
        </AppContext.Provider>
    );
}

export default AppContextProvider;
