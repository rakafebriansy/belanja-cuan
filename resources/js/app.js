import './bootstrap';
import 'preline';

document.addEventListener('livewire:navigated',() => {
    window.HSStaticMethods.autoInit(); // re-initialize preline component
});