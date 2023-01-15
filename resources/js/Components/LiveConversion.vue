<template>
    <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">Live Currency Conversion</h1>
        <p class="mt-2 text-sm text-gray-700">Select a source currency, and up to 5 currencies to get current conversion rates for</p>
        <div class="flex items-end">
            <div class="mr-4">
                <InputLabel for="source">Source Currency</InputLabel>
                <input type="text" v-model="source" id="source"/>
            </div>
            <div class="mr-4">
                <InputLabel for="currencies">Currencies to Convert</InputLabel>
                <input type="text" name="currencies" v-model="currencies" placeholder="AUD,EUR">
            </div>
            <PrimaryButton @click="getLiveConversionRates" :disabled="submitting">Convert Currencies</PrimaryButton>
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
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Conversion Rate</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(rate, key) in conversions">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ source }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ key }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ rate }}</td>
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

import {computed, ref} from "vue";
import axios from "axios";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const source = ref('USD');
const currencies = ref('');
const query = ref('');
const conversions = ref([]);
const conversionSource = ref('')
const submitting = ref(false);

const currenciesArray = computed(() => {
    return currencies.value.split(",")
})

function getLiveConversionRates() {
    if (submitting.value === true) {
        return;
    }

    submitting.value = true
    axios.get(`api/live-currency-conversion`, {
        params: {
            source: source.value,
            currencies: currenciesArray.value
        },
    }).then((response) => {
        conversions.value = response.data.conversions;
        conversionSource.value = response.data.source;
    }).catch((error) => {
        console.log(error)
        alert(error.response.data.message);
    }).finally(() => submitting.value = false);
}
</script>

