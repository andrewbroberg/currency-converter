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
                <select :value="reportType" id="intervals" v-model="reportType">
                    <option v-for="(name, int) in intervals" :value="int">{{ name }}</option>
                </select>
            </div>
            <PrimaryButton @click="submitReport" :disabled="submitting">Submit Report</PrimaryButton>
        </div>

        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Source Currency</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Currency</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="report in reports">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ report.source }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ report.currency }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ report.date }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ report.type }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ report.status }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><Link :href="route('historical-rate-report.show', report.id)">View</Link></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {onMounted, reactive, ref} from "vue";
import {Link} from "@inertiajs/inertia-vue3";

const reportType = ref('annual')
const date = ref();

const intervals = ref({
    annual: "Range: Yearly, Interval: Monthly",
    semiannual: "Range: Six Months, Interval: Weekly",
    monthly: "Range: One Month, Interval: Daily"
})
const reports = ref([]);
const source = ref('USD')
const conversionCurrency = ref('AUD')

const submitting = ref(false)

function submitReport() {
    axios.post('api/historical-rate-reports', {
        date: date.value,
        reportType: reportType.value,
        source: source.value,
        currency: conversionCurrency.value
    }).then(() => {
        getReports()
    }).catch((error) => {
        alert(error.response.data.message)
    })
}

function getReports() {
    axios.get('api/historical-rate-reports').then((response) => {
        reports.value =  response.data.data;
    });
}

onMounted(() => {
    getReports()
});

</script>

<style scoped>

</style>
