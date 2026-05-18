import Sidebar from "../components/Sidebar";
import SummaryCard from "../components/SummaryCard";

function Dashboard() {
  return (
    <div className="flex">
      
      <Sidebar />

      <div className="flex-1 bg-gray-100 min-h-screen p-8">

        <h1 className="text-3xl font-bold mb-8">
          Dashboard RABPay
        </h1>

        <div className="grid grid-cols-3 gap-6">

          <SummaryCard
            title="Total Saldo Virtual"
            value="Rp 250.000.000"
          />

          <SummaryCard
            title="Transaksi Berhasil"
            value="120"
          />

          <SummaryCard
            title="Transaksi Pending"
            value="15"
          />

          <SummaryCard
            title="Transaksi Ditolak"
            value="8"
          />

          <SummaryCard
            title="High Risk"
            value="3"
          />

        </div>

      </div>

    </div>
  );
}

export default Dashboard;