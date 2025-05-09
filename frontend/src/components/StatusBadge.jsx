export default function StatusBadge({ status }) {
  const statusStyles = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
  };

  const statusText = {
    pending: 'Bekliyor',
    in_progress: 'Devam Ediyor',
    completed: 'Tamamlandı',
  };

  return (
    <span
      className={`inline-block px-2 py-1 rounded-full text-sm ${statusStyles[status]}`}
    >
      {statusText[status]}
    </span>
  );
}