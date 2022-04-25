import './App.scss';
import 'antd/dist/antd.css';
import AppContextProvider from "./contexts/AppContext";
import Page from "./layout/Page";

function App() {

  return (
    <AppContextProvider>
      <Page />
    </AppContextProvider>
  );
}

export default App;
