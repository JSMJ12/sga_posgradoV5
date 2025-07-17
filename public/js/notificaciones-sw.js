import { register } from 'https://cdn.jsdelivr.net/npm/laravel-web-push@x.y.z/dist/laravel-web-push.es.js';
import axios from 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js';
register('/sw.js', {
  vapidPublicKey: 'BF-ex7ten2eUnA4DWkfCknWgYOjGQm8JXltuhlJwYnOKr9sOP8z8aEY0bKztBMLyTsVh7ggLyVodPxhcRpEbVms',
  callback: subscription => {
    axios.post('/push-subscribe', subscription)
      .then(() => console.log('Suscripción push guardada'))
      .catch(console.error);
  }
});
