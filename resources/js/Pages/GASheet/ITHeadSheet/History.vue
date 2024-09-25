<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { inject, onMounted, ref, reactive, computed } from "vue";
import { router, Head, Link, useForm } from "@inertiajs/vue3";
import { MqResponsive } from "vue3-mq";
import {
    mdiAccountKey,
    mdiPlus,
    mdiSquareEditOutline,
    mdiTrashCan,
    mdiAlertBoxOutline,
    mdiFileDocumentEdit,
    mdiAccountSwitch,
    mdiFilePdfBox,
    mdiClipboardListOutline,
    mdiWhatsapp,
    mdiCalendarMonth,
    mdiExportVariant,
    mdiFileDownloadOutline,
    mdiFileExcel,
    mdiAccountArrowUp,
    mdiArrowLeftBoldOutline,
    mdiMagnifyScan,
    mdiListBoxOutline,
    mdiEmailFast,
    mdiHistory,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import Multiselect from "vue-multiselect";
import Pagination from "@/Components/Admin/Pagination.vue";
import Sort from "@/Components/Admin/Sort.vue";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import PillTag from "@/Components/PillTag.vue";
import { pickBy, throttle } from "lodash";

// axios
import axios from "axios";

// moment
import moment from "moment";

import { TailwindPagination } from "laravel-vue-pagination";

const props = defineProps({
    //   myRoles: {
    //     type: Array,
    //     default: () => ([]),
    //   },
    //   master_status: {
    //     type: Array,
    //     default: () => ([]),
    //   },
    // user_id: {
    //   type: Number,
    //   default: () => (0),
    // },
    app_url: {
        type: String,
        default: () => "",
    },
    it_histories: {
        type: Array,
        default: () => [],
    },
    user_login: {
        type: Number,
        default: () => 0,
    },
});

onMounted(() => {
    // get_json_index();
    // check_signature();
    fixedData();
    getHistoryStatus(form.fixedArray);
});

const form = reactive({
    value: null,
    histories: props.it_histories,
    historyStatus: props.it_histories.map(
        (history) => history.history_status
    ),
    fixedArray: [],
    historyText: [],
});

const fixedData = () => {
    for (let i = 0; i < props.it_histories.length; i++) {
        const history = props.it_histories[i];
        if (
            history.history_status == 0 &&
            history.history_status != form.historyStatus[i - 1]
        ) {
            form.fixedArray.push(history);
        } else if (
            history.history_status == 1 &&
            history.history_status != form.historyStatus[i - 1]
        ) {
            form.fixedArray.push(history);
        } else if (
            history.history_status == 2 &&
            history.history_status != form.historyStatus[i - 1]
        ) {
            form.fixedArray.push(history);
        } else if (
            history.history_status == 3 &&
            history.history_status != form.historyStatus[i - 1]
        ) {
            form.fixedArray.push(history);
        } else if (
            history.history_status == 4 &&
            history.history_status != form.historyStatus[i - 1]
        ) {
            form.fixedArray.push(history);
        }
    }
};
const getHistoryStatus = (value) => {
    for (let i = 0; i < form.fixedArray.length; i++) {
        const history = value[i];
        if (history.history_status == 0) {
            if (history.user_update_id == props.user_login) {
                form.historyText[
                    i
                ] = `Anda Mengajukan maintenance dengan alat IT ${history.alat_name}`;
            } else {
                form.historyText[
                    i
                ] = `${history.user_update_name} Mengajukan maintenance dengan alat IT ${history.alat_name}`;
            }
        } else if (history.history_status == 1) {
            if (history.user_update_id == props.user_login) {
                form.historyText[
                    i
                ] = `Anda Mengubah komponen pada alat IT ${history.alat_name}`;
            } else {
                form.historyText[
                    i
                ] = `${history.user_update_name} Mengubah komponen pada alat IT ${history.alat_name}`;
            }
        } else if (history.history_status == 2) {
            if (history.user_update_id == props.user_login) {
                form.historyText[
                    i
                ] = `Anda Menyetujui maintenance alat IT ${history.alat_name}`;
            } else {
                form.historyText[
                    i
                ] = `${history.user_update_name} Menyetujui maintenance alat IT ${history.alat_name}`;
            }
        } else if (history.status == 3) {
            if (history.user_update_id == props.user_login) {
                form.historyText[
                    i
                ] = `Anda Menolak maintenance alat IT ${history.alat_name} dengan alasan ${history.reason_reject}`;
            } else {
                form.historyText[
                    i
                ] = `${history.user_update_name} Menolak maintenance alat IT ${history.alat_name} dengan alasan ${history.reason_reject}`;
            }
        } else {
            if (history.user_update_id == props.user_update_name) {
                form.historyText[i] = `Anda membatalkan maintenance alat IT`;
            } else {
                form.historyText[
                    i
                ] = `${history.user_update_name} membatalkan maintenance alat IT`;
            }
        }
    }

};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 1:
            return "bg-green-500 text-black text-xs";
        default:
            return "bg-red-500 text-black text-xs";
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 1:
            return "Active";
        default:
            return "Inactive";
    }
};

const getTypeLabel = (status) => {
    switch (status) {
        case 1:
            return "Cleaning";
        case 2:
            return "Maintenance";
        default:
            return "Tidak ada";
    }
};

const steps = [
    {
        target: ".step-1",
        content: "Harap menentukan TTD terlebih dahulu !",
    },
    {
        target: ".step-1",
        content: "Klik tombol next untuk menentukan TTD",
        placement: "bottom",
    },
];

const check_signature = () => {
    //HTTP request
    axios
        .get(`/sirkuler/createlist/check_signature`, {})
        .then((response) => {
            if (response.data.data == 0) {
                filter.is_show_signature = true;
            }
        })
        .catch(function (error) {
            if (error.response) {
                console.log(error.response);
            }
        });
};

const goToProfileSetting = () => {
    let url = `/admin/edit-account-info`;

    router.visit(url, {
        method: "get",
        data: {},
        replace: false,
        preserveState: false,
        preserveScroll: false,
        only: [],
        headers: {},
        errorBag: null,
        forceFormData: false,
        onCancelToken: (cancelToken) => {},
        onCancel: () => {},
        onBefore: (visit) => {},
        onStart: (visit) => {},
        onProgress: (progress) => {},
        onSuccess: (page) => {},
        onError: (errors) => {},
        onFinish: (visit) => {},
    });
};

const format = (date) => {
    const day = moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year = moment(date).format("YYYY");

    const hour = moment(date).format("H");
    const minute = moment(date).format("m");

    return `${day}/${month}/${year}`;
};

const formatDateTime = (date) => {
    const day = moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year = moment(date).format("YYYY");

    const hour = moment(date).format("HH");
    const minute = moment(date).format("mm");

    return `${day}/${month}/${year} ${hour}:${minute}`;
};

const cleaning_head = ref([]);


const filter = reactive({
    field_it_head_list: "",
    direction_it_head_list: "",
    search_it_head_list: "",
    search_status: "",

    is_show_signature: false,
});

const formDelete = useForm({});

const isModalDangerActive = ref(false);
const idDeleteModal = ref();

function destroy(id) {
    formDelete.delete(route("it_head.destroy", id));
}

function openModalDanger(value) {
    isModalDangerActive.value = true;
    idDeleteModal.value = value;
}

const swal = inject("$swal");
const disabled_resend = ref(false);
const buttonLoading = ref(false);
const timeout = ref(null);

const beforeDestroy = () => {
    // clear the timeout before the component is destroyed
    clearTimeout(timeout);
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="History Maintenance Sheet" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiHistory"
                title="History IT Sheet"
                main
            >
                <BaseButtons type="justify-start lg:justify-end">
                    <BaseButton
                        onclick="window.history.back()"
                        :icon="mdiArrowLeftBoldOutline"
                        label="Kembali"
                        color="white"
                        rounded-full
                        small
                    />
                </BaseButtons>
            </SectionTitleLineWithButton>

            <NotificationBar
                v-if="$page.props.flash.message"
                color="success"
                :icon="mdiAlertBoxOutline"
            >
                {{ $page.props.flash.message }}
            </NotificationBar>

            <CardBox
                class="shadow-lg"
                has-table
            >

            <div class="-mx-3 md:flex mb-6">
                <div class="md:w-full px-3 mb-6 md:mb-0">
                    <div class="p-2">
                        <div v-for="(data, index) in form.fixedArray" :key="index">
                            <div class="dy-chat dy-chat-end" v-if="data.user_update_id === props.user_login">
                                <div class="dy-chat-header">
                                    <span class="text-slate-700 pr-1">{{ form.fixedArray[index].user_update_name }}</span>
                                    <time class="text-xs opacity-50 text-slate-700">{{ form.fixedArray[index].date }}</time>
                                </div>
                                <div class="dy-chat-bubble dy-chat-bubble-info" v-if="form.fixedArray[index].history_status === 0 || form.fixedArray[index].history_status === 1">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-success" v-else-if="form.fixedArray[index].history_status === 2">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-error" v-else-if="form.fixedArray[index].history_status === 3">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-warning" v-else-if="form.fixedArray[index].history_status === 4">{{ form.historyText[index] }}</div>
                            </div>
                            <div class="dy-chat dy-chat-start" v-else>
                                <div class="dy-chat-header">
                                    <span class="text-slate-700 pr-1">{{ form.fixedArray[index].user_update_name }}</span>
                                    <time class="text-xs opacity-50 text-slate-700">{{ form.fixedArray[index].date }}</time>
                                </div>
                                <div class="dy-chat-bubble dy-chat-bubble-info" v-if="form.fixedArray[index].history_status === 0 || form.fixedArray[index].history_status === 1">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-success" v-else-if="form.fixedArray[index].history_status === 2">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-error" v-else-if="form.fixedArray[index].history_status === 3">{{ form.historyText[index] }}</div>
                                <div class="dy-chat-bubble dy-chat-bubble-warning" v-else-if="form.fixedArray[index].history_status === 4">{{ form.historyText[index] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
                
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>


