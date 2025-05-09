import { Dialog, Transition } from '@headlessui/react';
import { Fragment } from 'react';

export default function ConfirmationModal({ isOpen, onClose, onConfirm, message }) {
  return (
    <Transition appear show={isOpen} as={Fragment}>
      <Dialog as="div" className="relative z-10" onClose={onClose}>
        <Transition.Child
          as={Fragment}
          enter="ease-out duration-300"
          enterFrom="opacity-0"
          enterTo="opacity-100"
          leave="ease-in duration-200"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
        >
          <div className="fixed inset-0 bg-black bg-opacity-25" />
        </Transition.Child>

        <div className="fixed inset-0 overflow-y-auto">
          <div className="flex min-h-full items-center justify-center p-4 text-center">
            <Transition.Child
              as={Fragment}
              enter="ease-out duration-300"
              enterFrom="opacity-0 scale-95"
              enterTo="opacity-100 scale-100"
              leave="ease-in duration-200"
              leaveFrom="opacity-100 scale-100"
              leaveTo="opacity-0 scale-95"
            >
              <Dialog.Panel className="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 text-left align-middle shadow-xl transition-all">
                <Dialog.Title as="h3" className="text-lg font-medium leading-6">
                  Onay
                </Dialog.Title>
                <div className="mt-2">
                  <p className="text-sm text-gray-500 dark:text-gray-400">{message}</p>
                </div>
                <div className="mt-4 flex justify-end space-x-2">
                  <button
                    type="button"
                    className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                    onClick={onClose}
                  >
                    İptal
                  </button>
                  <button
                    type="button"
                    className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700"
                    onClick={onConfirm}
                  >
                    Sil
                  </button>
                </div>
              </Dialog.Panel>
            </Transition.Child>
          </div>
        </div>
      </Dialog>
    </Transition>
  );
}