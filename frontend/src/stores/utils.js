import { defineStore } from 'pinia'

export const useUtilsStore = defineStore('utils', () => {
  // String functions
  const capitalizeFirst = (str) => {
    if (!str) return ''
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()
  }

  const capitalizeWords = (str) => {
    if (!str) return ''
    return str.replace(/\w\S*/g, (txt) => {
      return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
    })
  }

  const truncateText = (str, length = 50) => {
    if (!str) return ''
    return str.length > length ? str.substring(0, length) + '...' : str
  }

  const formatPhoneNumber = (phone) => {
    if (!phone) return ''
    // Format: +31 6 12 34 56 78
    const cleaned = phone.replace(/\D/g, '')
    if (cleaned.length === 10 && cleaned.startsWith('06')) {
      return `+31 ${cleaned.substring(1, 2)} ${cleaned.substring(2, 4)} ${cleaned.substring(4, 6)} ${cleaned.substring(6, 8)} ${cleaned.substring(8, 10)}`
    }
    return phone
  }

  // Date functions
  const formatDate = (date, locale = 'nl-NL') => {
    if (!date) return ''
    return new Date(date).toLocaleDateString(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  const formatDateTime = (date, locale = 'nl-NL') => {
    if (!date) return ''
    return new Date(date).toLocaleDateString(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const isToday = (date) => {
    if (!date) return false
    const today = new Date()
    const checkDate = new Date(date)
    return today.toDateString() === checkDate.toDateString()
  }

  // Number functions
  const formatCurrency = (amount, currency = 'EUR', locale = 'nl-NL') => {
    return new Intl.NumberFormat(locale, {
      style: 'currency',
      currency: currency
    }).format(amount)
  }

  const formatNumber = (number, locale = 'nl-NL') => {
    return new Intl.NumberFormat(locale).format(number)
  }

  // Validation functions
  const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  }

  const isValidPhone = (phone) => {
    const phoneRegex = /^(\+31|0|0031)[1-9][0-9]{8}$/
    return phoneRegex.test(phone.replace(/\s/g, ''))
  }

  // Array functions
  const sortByKey = (array, key, direction = 'asc') => {
    return [...array].sort((a, b) => {
      const aVal = a[key]
      const bVal = b[key]
      
      if (direction === 'asc') {
        return aVal < bVal ? -1 : aVal > bVal ? 1 : 0
      } else {
        return aVal > bVal ? -1 : aVal < bVal ? 1 : 0
      }
    })
  }

  const filterByKey = (array, key, value) => {
    return array.filter(item => item[key] === value)
  }

  // Color functions
  const getRandomColor = () => {
    const colors = [
      'bg-blue-500', 'bg-green-500', 'bg-purple-500', 
      'bg-pink-500', 'bg-orange-500', 'bg-teal-500',
      'bg-cyan-500', 'bg-indigo-500', 'bg-amber-500'
    ]
    return colors[Math.floor(Math.random() * colors.length)]
  }

  const getColorForString = (str) => {
    const colors = [
      'bg-blue-500', 'bg-green-500', 'bg-purple-500', 
      'bg-pink-500', 'bg-orange-500', 'bg-teal-500',
      'bg-cyan-500', 'bg-indigo-500', 'bg-amber-500'
    ]
    
    let hash = 0
    for (let i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash)
    }
    
    return colors[Math.abs(hash) % colors.length]
  }

  return {
    // String
    capitalizeFirst,
    capitalizeWords,
    truncateText,
    formatPhoneNumber,
    
    // Date
    formatDate,
    formatDateTime,
    isToday,
    
    // Number
    formatCurrency,
    formatNumber,
    
    // Validation
    isValidEmail,
    isValidPhone,
    
    // Array
    sortByKey,
    filterByKey,
    
    // Color
    getRandomColor,
    getColorForString
  }
})