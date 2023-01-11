<template>
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Historical Rates Report</h1>
        <p class="mt-2 text-sm text-gray-700">Request a report of historical conversion rate</p>
        <div class="flex items-end">
            <div class="mr-4">
                <InputLabel for="date">Date</InputLabel>
                <input type="date" id="date" v-model="date">
            </div>
            <div class="mr-4">
                <InputLabel for="source">Source Currency</InputLabel>
                <input type="text" id="source" v-model="source">
            </div>
            <div class="mr-4">
                <InputLabel for="conversion_currency">Conversion Currency</InputLabel>
                <input type="text" id="conversion_currency" v-model="conversionCurrency">
            </div>
            <div class="mr-4">
                <InputLabel for="intervals">Report Type</InputLabel>
                <select :value="reportType" id="intervals">
                    <option v-for="(name, int) in intervals" :value="int">{{ name }}</option>
                </select>
            </div>
            <PrimaryButton @click="submitReport" :disabled="submitting">Submit Report</PrimaryButton>
        </div>
    </div>
</template>

<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {ref} from "vue";

const reportType = ref('yearly')
const date = ref();

const intervals = ref({
    yearly: "Range: Yearly, Interval: Monthly",
    semiannual: "Range: Six Months, Interval: Weekly",
    monthly: "Range: One Month, Interval: Daily"
})

const source = ref('USD')
const conversionCurrency = ref('AUD')

const submitting = ref(false)

function submitReport() {
    axios.post('api/historical-rate-reports', {
        date,
        reportType
    })
}

</script>

<style scoped>

</style>
