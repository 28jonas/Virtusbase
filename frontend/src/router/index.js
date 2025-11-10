import { createRouter, createWebHashHistory,createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Dashboard from '../views/Dashboard.vue'
import Families from '../views/Families.vue'
import Calendar from '../views/Calendar.vue'
import Shopping from '../views/Shopping.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import FamilyDetail from '../views/FamilyDetail.vue'
import Todo from '../views/Todos.vue'
import Habits from '../views/Habits.vue'
import BankingCard from '../views/BankingCard.vue'
import Finance from '../views/Finance.vue'
import Sport from '../views/Sport.vue'
import MealTracking from '../views/MealTracking.vue'
import FoodItemsList from '../views/FoodItemsList.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/families',
    name: 'Families',
    component: Families,
    meta: { requiresAuth: true }
  },
  {
    path: '/families/:id', // Dynamic route parameter
    name: 'FamilyDetail',
    component: FamilyDetail,
    props: true // Optioneel: params als props doorgeven
  },

  {
    path: '/calendar',
    name: 'Calendar',
    component: Calendar,
    meta: { requiresAuth: true }
  },
  {
    path: '/shopping',
    name: 'Shopping',
    component: Shopping,
    meta: { requiresAuth: true }
  },
  {
    path: '/todos',
    name: 'Todo',
    component: Todo,
    meta: { requiresAuth: true }
  },
  {
    path: '/habits',
    name: 'Habits',
    component: Habits,
    meta: { requiresAuth: true }
  },
  {
    path: '/bankingcards',
    name: 'BankingCards',
    component: BankingCard,
    meta: { requiresAuth: true }
  },
  {
    path: '/finance',
    name: 'Finance',
    component: Finance,
    meta: { requiresAuth: true }
  },
  {
    path: '/sport',
    name: 'Sport',
    component: Sport,
    meta: { requiresAuth: true }
  },
   {
    path: '/meals',
    name: 'meals',
    component: MealTracking,
    meta: { requiresAuth: true }
  },
  {
    path: '/food-items',
    name: 'food-items',
    component: FoodItemsList,
    meta: { requiresAuth: true }
  },
]

const router = createRouter({
  //history: createWebHashHistory(),
  history: createWebHistory(),
  routes
})

// router.beforeEach((to, from, next) => {
//   const authStore = useAuthStore()

//   if (to.meta.requiresAuth && !authStore.isAuthenticated) {
//     next('/login')
//   } else if (!to.meta.requiresAuth && authStore.isAuthenticated) {
//     next('/')
//   } else {
//     next()
//   }
// })


export default router