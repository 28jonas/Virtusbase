<template>
  <div class="grid grid-cols-12 lg:grid-rows-[1fr_1fr] lg:h-[calc(100vh-110px)] gap-4">
    <!-- Left part -->
    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <BankingCards />
    </div>

    <div class="col-span-12 lg:col-span-6 flex flex-col h-full gap-4">
      <FinanceChart />
    </div>

    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <Transactions />
    </div>

    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <IncomeExpenseStats />
    </div>

    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <FinancialGoals />
    </div>

    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <Expenses />
    </div>

    <div class="col-span-12 lg:col-span-3 flex flex-col h-full">
      <Incomes />
    </div>
  </div>
</template>

<script setup>
import BankingCards from '../components/finance/BankingCard.vue'
import FinanceChart from '../components/finance/FinanceChart.vue'
import Transactions from '../components/finance/Transactions.vue'
import IncomeExpenseStats from '../components/finance/IncomeExpeseStats.vue'
import FinancialGoals from '../components/finance/FinancialGoals.vue'
import Expenses from '../components/finance/Expenses.vue'
import Incomes from '../components/finance/Incomes.vue'
</script>