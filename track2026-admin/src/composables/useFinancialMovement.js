// src/composables/useFinancialMovement.js

import { computed } from 'vue'


const typeMap = {

  earning: {

    icon: 'IconArrowUpCircle',

    border: 'bg-emerald-500',

    background: 'bg-emerald-500/20',

    iconColor: 'text-emerald-300',

    titleColor: 'text-emerald-300',

    amount: 'text-emerald-300',

  },


  bonus: {

    icon: 'IconGift',

    border: 'bg-yellow-500',

    background: 'bg-yellow-500/20',

    iconColor: 'text-yellow-300',

    titleColor: 'text-yellow-300',

    amount: 'text-yellow-300',

  },


  penalty: {

    icon: 'IconAlertTriangle',

    border: 'bg-purple-500',

    background: 'bg-purple-500/20',

    iconColor: 'text-purple-300',

    titleColor: 'text-purple-300',

    amount: 'text-purple-300',

  },


  deduction: {

    icon: 'IconArrowDownCircle',

    border: 'bg-red-500',

    background: 'bg-red-500/20',

    iconColor: 'text-red-300',

    titleColor: 'text-red-300',

    amount: 'text-red-300',

  },

}



export function useFinancialMovement(item) {


  const typeStyle = computed(() => {

    return typeMap[item.value?.type]
      ?? typeMap.earning

  })



  function formatAmount(value) {

    return Number(value ?? 0)
      .toLocaleString('es-CO', {

        style: 'currency',

        currency: 'USD',

        minimumFractionDigits: 2,

      })

  }



  function formatTime(data) {

    if (data?.time) {

      return data.time

    }


    if (!data?.date) {

      return ''

    }


    return new Date(data.date)
      .toLocaleTimeString('es-CO', {

        hour: '2-digit',

        minute: '2-digit',

      })

  }



  return {

    typeStyle,

    formatAmount,

    formatTime,

  }

}