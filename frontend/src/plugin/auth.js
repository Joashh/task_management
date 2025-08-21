import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const user = ref(null)

  const isAuthenticated = computed(() => !!user.value)

  state: () => ({
  user: null,
  isLoading: false // Add this
})

  // Axios instance with cookies
  const api = axios.create({
    baseURL: 'http://localhost:8000',
    withCredentials: true, // IMPORTANT: sends cookies
  })

  function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

  // Login
  const login = async (formData) => {
  try {
    // Get CSRF cookie first
    await api.get('/sanctum/csrf-cookie');

    // Read the token from the cookie
    const xsrfToken = getCookie('XSRF-TOKEN');

    // Send login request with X-XSRF-TOKEN header
    const response = await api.post('/api/login', formData, {
      headers: {
        'X-XSRF-TOKEN': xsrfToken,
      },
    });

    user.value = response.data.user;
    router.push('/home');
  } catch (err) {
    console.error(err);
    throw err;
  }
};


  // Logout
  // Logout
const logout = async () => {
  try {
    // Get fresh CSRF token (optional but safe)
    await api.get('/sanctum/csrf-cookie');
    // Send logout request to the correct API endpoint
    await api.post('/api/logout', null, {
      headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
    });
  } catch (err) {
    console.error('Logout failed:', err.response?.data || err.message);
    throw err; // Re-throw to handle in components (e.g., show error)
  } finally {
    user.value = null; // Clear client-side state
    // Clear the laravel_session cookie (critical for preventing re-fetch)
    document.cookie = 'laravel_session=; path=/; domain=localhost; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';
    // Redirect to login AFTER clearing state and cookies
    router.push('/login');
  }
};





  // Fetch current user (if already logged in via cookie)
  const fetchUser = async () => {
    try {
      const response = await api.get('/api/user')
      user.value = response.data
    } catch {
      user.value = null
    }
  }

  return { user, isAuthenticated, login, logout, fetchUser }
})
