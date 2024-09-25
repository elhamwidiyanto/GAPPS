<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import {
    mdiAccountArrowUp,
    mdiArrowLeftBoldOutline,
    mdiFileDocumentPlusOutline,
} from "@mdi/js";
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

const props = defineProps({
    app_url: {
        type: String,
        default: "",
    },
    master_simbol_kondisis: {
        type: Array,
        default: () => [],
    },
    master_lokasis: {
        type: Array,
        default: () => [],
    },
    master_gedungs: {
        type: Array,
        default: () => [],
    },
    master_ruangans: {
        type: Array,
        default: () => [],
    },
    master_pics: {
        type: Array,
        default: () => [],
    },
    master_alats: {
        type: Array,
        default: () => [],
    },
    master_komponens: {
        type: Array,
        default: () => [],
    },
    alat_choosed: {
        type: Object,
        default: () => [],
    },
});

const { numberFormat } = useNumberFormat();

const formatDateTime = (date) => {
    const day = moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year = moment(date).format("YYYY");
    const hour = moment(date).format("HH");
    const minute = moment(date).format("mm");
    return `${day}/${month}/${year} ${hour}:${minute}`;
};

const swal = inject("$swal");
const disabled = ref(false);
const form = reactive({
    name: "",
    is_checked: false,
    items: [],
    simbol: null,
    value_location: null,
    value_gedung: null,
    value_ruangan: null,
    master_alats: [],
    master_simbol_kondisis: [],
    description: [],
    selected_value: [],
    is_checked: [],
    id: [],
    value_pic: [],
    value_alat: [],
    master_komponens: [],
    allChecklistValue: "",
    alat_qr: "",
    value_alat_post: "",
    check_submit: "",
});

const simbol_kondisi = ref([]);
const alat = ref([]);
const locations = ref([]);
const buildings = ref([]);
const rooms = ref([]);

onMounted(() => {
    get_json_index();
    if (props.alat_choosed != "" || props.alat_choosed != null) {
        qrValue();
    }
});

const qrValue = () => {
    if (props.alat_choosed != "") {
        form.alat_qr = props.alat_choosed;
        form.value_alat = props.alat_choosed;
        changeAlatQr(form.alat_qr);
    } else {
        form.alat_qr = null;
    }
};

const get_json_index = async (page = 1, field = "") => {
    // your logic for fetching data
};

const changeLocation = () => {
    form.value_gedung = null;
    form.value_ruangan = null;
    form.value_alat = null;
};

const changeGedung = () => {
    form.value_ruangan = null;
    form.value_alat = null;
};

const changeAlat = async (value) => {
    try {
        const response = await axios.post(
            `/ga_sheet/maintenance/get_komponen_simbol_kondisi`,
            {
                value_location: form.value_location,
                value_gedung: form.value_gedung,
                value_ruangan: form.value_ruangan,
                value_alat: value,
            }
        );
        if (!value) {
            form.master_komponens = [];
        } else {
            form.master_komponens = response.data.data_komponen;
            form.master_simbol_kondisis = response.data.data_simbol_kondisi;
            form.check_submit = 2;
            form.selected_value = response.data.default_simbol_kondisi;
        }
    } catch (error) {
        console.error(error);
    }
};

const changeAlatQr = async (value) => {
    try {
        const response = await axios.post(
            `/ga_sheet/maintenance/get_komponen_simbol_kondisi_qr`,
            {
                value_alat: value,
            }
        );
        if (!value) {
            form.master_komponens = [];
        } else {
            form.master_komponens = response.data.data_komponen;
            form.master_simbol_kondisis = response.data.data_simbol_kondisi;
            form.value_alat = form.alat_qr;
            form.check_submit = 1;
            form.selected_value = response.data.default_simbol_kondisi;
            // form.description = response.data.data_komponen.map(() => '')
        }
    } catch (error) {
        console.error(error);
    }
};

const delay = () => {
    if (form.check_submit == 1) {
        form.value_alat_post = props.alat_choosed;
    } else {
        form.value_alat_post = form.value_alat;
    }

    disabled.value = true;

    axios
        .post(`/ga_sheet/maintenance/check_create_today`, {
            id_alat: form.value_alat_post.id,
        })
        .then((response) => {
            if (response.data.success) {
                swal({
                    title: "Berhasil!",
                    text: response.data.success,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });
                submit();
            }
        })
        .catch((error) => {
            if (error.response && error.response.data.error) {
                swal({
                    title: "Error!",
                    text: error.response.data.error,
                    icon: "error",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
            disabled.value = false;
        });
};

const submit = () => {
    router.post(
        `/ga_sheet/maintenance`,
        {
            master_alats: form.value_alat_post,
            simbol_name: form.selected_value,
            is_checked: form.is_checked,
            description: form.description,
            pic: form.value_pic,
            location: form.value_location,
            gedung: form.value_gedung,
            ruangan: form.value_ruangan,
            master_komponens: form.master_komponens,
        },
        {
            onSuccess: (Error) => {
                //show success alert
                console.log(Error);
                swal({
                    title: "Berhasil!",
                    text: "Cleaning berhasil ditambahkan!",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });
            },
            onError: (Error) => {
                console.log(Error);
                disabled.value = false;
            },
        }
    );
};

const filteredGedung = computed(() => {
    if (form.value_location) {
        return props.master_gedungs.filter(
            (gedung) => gedung.id_lokasi === form.value_location.id
        );
    }
    return [];
});

const filteredRuangan = computed(() => {
    if (form.value_gedung) {
        return props.master_ruangans.filter(
            (ruangan) => ruangan.id_gedung === form.value_gedung.id
        );
    }
    return [];
});

const filteredAlat = computed(() => {
    if (form.value_ruangan) {
        return props.master_alats.filter(
            (alat) => alat.ruangan_id === form.value_ruangan.id
        );
    }
    return [];
});

const selectValue = () => {
    for (let i = 0; i < form.master_alats.length; i++) {
        if (!form.selected_value[i]) {
            form.selected_value[i] = { id: null, name: null }; // Ensure a consistent structure
        }
    }
};

const allChecklist = () => {
    if (form.allChecklistValue == true) {
        for (let i = 0; i < form.master_komponens.length; i++) {
            form.is_checked[i] = false;
        }
    } else {
        for (let i = 0; i < form.master_komponens.length; i++) {
            form.is_checked[i] = true;
        }
    }
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Maintenance Sheet" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFileDocumentPlusOutline"
                title="Tambah Maintenance Alat"
                main
            >
                <BaseButton
                    :route-name="route('maintenance.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 lg:w-1/5 px-3 mb-6 lg:mb-0">
                    <FormField label="Lokasi" :required="true">
                        <Multiselect
                            v-model="form.value_location"
                            :options="props.master_lokasis"
                            placeholder="Pilih Lokasi"
                            label="name"
                            @update:modelValue="changeLocation"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/5 px-3 mb-6 lg:mb-0">
                    <FormField label="Gedung" :required="true">
                        <Multiselect
                            v-model="form.value_gedung"
                            :options="filteredGedung"
                            placeholder="Pilih Gedung"
                            @update:modelValue="changeGedung"
                            label="name"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/5 px-3 mb-6 lg:mb-0">
                    <FormField label="Ruangan" :required="true">
                        <Multiselect
                            v-model="form.value_ruangan"
                            :options="filteredRuangan"
                            placeholder="Pilih Ruangan"
                            label="name"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/5 px-3 mb-6 lg:mb-0">
                    <FormField label="Alat" :required="true">
                        <div
                            v-if="
                                props.alat_choosed == '' ||
                                props.alat_choosed == null
                            " 
                        >
                            <Multiselect
                                v-model="form.value_alat"
                                :options="filteredAlat"
                                placeholder="Pilih alat"
                                @update:modelValue="changeAlat"
                                label="name"
                                track-by="id"
                            />
                        </div>
                        <div v-else>
                            <div qrValue>
                                <Multiselect
                                    v-model="form.alat_qr"
                                    :options="filteredAlat"
                                    placeholder="Pilih alat"
                                    label="name"
                                    :disabled="true"
                                    track-by="id"
                                />
                            </div>
                        </div>
                    </FormField>
                </div>
                <div class="w-full sm:w-1/2 lg:w-1/5 px-3 mb-6 lg:mb-0">
                    <FormField label="Nama PIC" :required="true">
                        <Multiselect
                            v-model="form.value_pic"
                            :options="props.master_pics"
                            placeholder="Pilih PIC"
                            label="pic_name"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div class="sm:invisible w-full flex justify-end p-4" v-if="form.master_komponens.length > 0">
                     
                    <input
                        name="data.form.checkbox"
                        type="checkbox"
                        v-model="form.allChecklistValue"
                        @click="allChecklist(form.allChecklistValue)"
                    />
                    <span class="ml-3 -mt-1">Check All</span>
                </div>
            </div>
            <CardBox
                class="mb-6"
                has-table
                v-if="form.master_komponens.length > 0"
            >
                <div class="flex justify-center overflow-x-auto">
                    <table
                        class="table table-responsive px-auto text-slate-950 dark:text-slate-50"
                    >
                        <thead>
                            <tr>
                                <th>
                                    <div
                                        class="flex items-center justify-center gap-4 cursor-pointer m-1"
                                    >
                                        <span
                                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >No</span
                                        >
                                    </div>
                                </th>
                                <th>
                                    <div
                                        class="flex items-center justify-center gap-4 cursor-pointer m-1"
                                    >
                                        <span
                                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >Facility</span
                                        >
                                    </div>
                                </th>
                                <th>
                                    <div
                                        class="flex items-center justify-center gap-2 cursor-pointer m-1"
                                    >
                                        <label
                                            class="ml-2 no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >Check All</label
                                        >
                                        <input
                                            name="data.form.checkbox"
                                            type="checkbox"
                                            v-model="form.allChecklistValue"
                                            @click="
                                                allChecklist(
                                                    form.allChecklistValue
                                                )
                                            "
                                        />
                                    </div>
                                </th>
                                <th>
                                    <div
                                        class="flex items-center justify-center gap-4 cursor-pointer m-1"
                                    >
                                        <span
                                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >Status</span
                                        >
                                    </div>
                                </th>
                                <th>
                                    <div
                                        class="flex items-center justify-center gap-4 cursor-pointer m-1"
                                    >
                                        <span
                                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >Remark</span
                                        >
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(data, id) in form.master_komponens"
                                :key="id"
                            >
                                <td class="text-center">{{ id + 1 }}</td>
                                <td class="text-center">{{ data.name }}</td>
                                <td style="text-align: center">
                                    <input
                                        name="data.form.checkbox"
                                        type="checkbox"
                                        v-model="form.is_checked[id]"
                                    />
                                </td>
                                <td>
                                    <Multiselect
                                        v-model="form.selected_value[id]"
                                        :options="form.master_simbol_kondisis"
                                        placeholder="Pilih Salah Satu"
                                        label="name"
                                        track-by="id"
                                        open-direction="bottom"
                                    />
                                </td>
                                <td style="text-align: center">
                                    <textarea
                                        id="description"
                                        v-model="form.description[id]"
                                        class="text-black textarea textarea-bordered rounded-md"
                                        placeholder="Remark"
                                    ></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <template #footer>
                    <BaseButtons class="flex justify-end">
                        <BaseButton
                            type="submit"
                            color="success"
                            label="Simpan"
                            :class="{ 'opacity-25': disabled }"
                            :disabled="disabled"
                            @click="delay"
                        />
                    </BaseButtons>
                </template>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
