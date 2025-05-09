export default function PriorityIndicator({ priority }) {
  const priorityStyles = {
    low: 'bg-gray-200 text-gray-800',
    medium: 'bg-orange-200 text-orange-800',
    high: 'bg-red-200 text-red-800',
  };

  const priorityText = {
    low: 'Düşük',
    medium: 'Orta',
    high: 'Yüksek',
  };

  return (
    <span
      className={`inline-block px-2 py-1 rounded-full text-sm ${priorityStyles[priority]}`}
    >
      {priorityText[priority]}
    </span>
  );
}