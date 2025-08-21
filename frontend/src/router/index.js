import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/plugin/auth';
import Login from '@/Pages/login.vue';
import Register from '@/Pages/register.vue';
import Home from '@/Pages/home.vue';
import About from '@/Pages/about.vue';

const routes = [
  { path: '/login', name: 'Login', component: Login, meta: { guest: true } },
  { path: '/register', name: 'Register', component: Register, meta: { guest: true } },
  { path: '/home', name: 'Home', component: Home, meta: { requiresAuth: true } },
  { path: '/about', name: 'About', component: About, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Route guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  // If user state is unknown but session exists, fetch it
  if (auth.user === null) {
    try {
      await auth.fetchUser(); // fetch user from backend
    } catch {
      auth.user = null;
    }
  }

  // Redirect logged-in users away from guest pages
  if (to.meta.guest && auth.isAuthenticated) {
    return next({ name: 'Home' });
  }

  // Redirect non-authenticated users from protected pages
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'Login' });
  }

  next();
});




export default router;
