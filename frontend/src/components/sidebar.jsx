import { Link } from "react-router-dom";

function Sidebar() {
  return (
    <div className="w-64 h-screen bg-gray-900 text-white p-5">
      <h1 className="text-2xl font-bold mb-8">RABPay</h1>

      <ul className="space-y-4">
        <li><Link to="/">Dashboard</Link></li>
        <li><Link to="/bidang">Data Bidang</Link></li>
        <li><Link to="/vendor">Data Vendor</Link></li>
        <li><Link to="/rab">Data RAB</Link></li>
        <li><Link to="/simulasi">Simulasi Transaksi</Link></li>
        <li><Link to="/riwayat">Riwayat Transaksi</Link></li>
        <li><Link to="/audit">Audit Trail</Link></li>
        <li><Link to="/risk">Risk Monitoring</Link></li>
      </ul>
    </div>
  );
}

export default Sidebar;