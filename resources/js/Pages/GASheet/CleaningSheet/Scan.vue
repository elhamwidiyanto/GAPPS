<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { StreamBarcodeReader } from "vue-barcode-reader";
import { onMounted, inject, reactive, ref, computed } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import { mdiAccountArrowUp, mdiArrowLeftBoldOutline, mdiQrcode } from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Multiselect from "vue-multiselect";
import moment from "moment";
import useNumberFormat from "../../../Helpers/numberFormat";
import BaseDivider from "@/Components/BaseDivider.vue";
import axios from "axios";
import { forEach } from "lodash";

const isCameraAvailable = ref(false);
const text = ref("");
const error = ref("");
const valueArray = ref[""];

onMounted(() => {});

const onDecode = (a, b, c) => {
    text.value = a;

    if (text.value) {
        router.post(`/ga_sheet/cleaning/create_qr`, {
            id_ruangan: text.value,
        });
        // window.location.href = text.value;
    }
};

const onLoaded = () => {
    console.log("Camera loaded");
};

const onError = (err) => {
    error.value = `Error accessing camera: ${err.message}`;
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Scan" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiQrcode"
                title="Scan Cleaning"
                main
            >
                <BaseButtons type="justify-start lg:justify-end">
                    <BaseButton
                        :route-name="route('cleaning.index')"
                        :icon="mdiArrowLeftBoldOutline"
                        label="Kembali"
                        color="white"
                        rounded-full
                        small
                    />
                </BaseButtons>
            </SectionTitleLineWithButton>
            <div>
                <div class="centered-container">
                    <StreamBarcodeReader
                        @decode="(a, b, c) => onDecode(a, b, c)"
                        @loaded="() => onLoaded()"
                    ></StreamBarcodeReader>
                    Input Value: {{ text || "Nothing" }}
                </div>
            </div>
            <a></a>
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style scoped>
.centered-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.dateFilter {
    width: 100%;
    height: 38px;
}
</style>
