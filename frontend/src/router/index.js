import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Publieke views (zonder layout)
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'

// Beschermde views (met sidebar/header layout)
import Dashboard from '../views/Dashboard.vue'
import Families from '../views/Families.vue'
import Calendar from '../views/Calendar.vue'
import Shopping from '../views/Shopping.vue'
import FamilyDetail from '../views/FamilyDetail.vue'
import Todo from '../views/Todos.vue'
import Habits from '../views/Habits.vue'
import BankingCard from '../views/BankingCard.vue'
import Finance from '../views/Finance.vue'
import Sport from '../views/Sport.vue'
import MealTracking from '../views/MealTracking.vue'
import FoodItemsList from '../views/FoodItemsList.vue'

const routes = [
  // Publieke routes (geen auth nodig, geen layout)
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false, layout: 'empty' }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false, layout: 'empty' }
  },

  // Beschermde routes (auth nodig, met layout)
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/families',
    name: 'Families',
    component: Families,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/families/:id',
    name: 'FamilyDetail',
    component: FamilyDetail,
    props: true,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: Calendar,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/shopping',
    name: 'Shopping',
    component: Shopping,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/todos',
    name: 'Todo',
    component: Todo,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/habits',
    name: 'Habits',
    component: Habits,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/bankingcards',
    name: 'BankingCards',
    component: BankingCard,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/finance',
    name: 'Finance',
    component: Finance,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/sport',
    name: 'Sport',
    component: Sport,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/meals',
    name: 'meals',
    component: MealTracking,
    meta: { requiresAuth: true, layout: 'default' }
  },
  {
    path: '/food-items',
    name: 'food-items',
    component: FoodItemsList,
    meta: { requiresAuth: true, layout: 'default' }
  },

  // Redirect naar login voor onbekende routes
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Auth guard opnieuw inschakelen
// router.beforeEach((to, from, next) => {
//   const authStore = useAuthStore()
//   console.log('Navigating to:', to.fullPath)
//   console.log('User is authenticated:', authStore.isAuthenticated)
//   if (to.meta.requiresAuth && !authStore.isAuthenticated) {
//     console.log('Route requires auth, but user is not authenticated. Redirecting to login.')
//     next('/login')
//   } else if (!to.meta.requiresAuth && authStore.isAuthenticated && (to.name === 'Login' || to.name === 'Register')) {
//     console.log('Route does not require auth, but user is authenticated. Redirecting to dashboard.')
//     next('/')
//   } else {
//     next()
//   }
// })

export default router