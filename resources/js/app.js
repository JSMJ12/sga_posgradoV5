import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


window.Echo = new Echo({
  broadcaster: 'pusher',
  key: 'b0d28aca280947c65ff5',
  cluster: 'us2',
  forceTLS: true
});


