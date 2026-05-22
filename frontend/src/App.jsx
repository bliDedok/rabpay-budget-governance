import { useState } from "react";
import "./App.css";
import Logo from "./assets/hero.png";
import LogoAtas from "./assets/react.svg";
import ThunderLogo from "./assets/vite.svg";

function App() {
const [count, setCount] = useState(0);

return (
<div className="container-box">
<div style={{display:"flex", justifyContent: "center", alignItems: "center", gap: "20px", marginTop: "50px", marginBottom: "15px"}}>

  {/* Logo 1: Logo */}
  <img
   src={reactLogo}
   alt="Logo React"
   style={{ width: "50px", height: "50px", animation: "Logo-spin infinite 20s linear" }}
   />
   {/* Logo 2: LogoAtas*/}
   <img
   src={LogoAtas}
   alt="LogoAtas"
   stylle={{ width: "90px", height: "auto" }}
   />
   {/* Logo 3: thunderLogo */}
   <img
   src={ThunderLogo}
   alt="ThunderLogo"
   style={{ width: "50px", height: "50px"}}
   />

  </div>  

{/*Judul Dashboard*/}
<h1 style={{ fontSize: "50px", marginTop: "10px", marginBottom: "20px"}}>
  Dashboard
</h1>


  {/* UI Bawaan Tombol Count */}  
  <div className="card">
    <button onClick={() => setCount((count) => count + 1)}>
      count is {count}
    </button>
    
  </div>

  <p className="read-the-docs">
    Click on the Vite and React logos to learn more
  </p>
  </div>
);
}
export default App;