<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import { mdiAccountArrowUp,
    mdiArrowLeftBoldOutline,
    mdiBroom,
    mdiFileDocumentPlusOutline, } from "@mdi/js";
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
    master_pics: {
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
    // lokasi_choosed: {
    //     type: Object,
    //     default: () => [],
    // },
    // gedung_choosed: {
    //     type: Object,
    //     default: () => [],
    // },
    ruangan_choosed: {
        type: Object,
        default: () => null,
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

const checkAll = () => {
    this.form.is_checked = this.form.is_checked.map(() => this.isChecked);
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
    selected_pic: [],
    allChecked: false,
    check_submit: 2,
    disabledForm: false,
});

const delay = async (value) => {
    if (form.date == "" && form.name == null) {
        swal({
            title: "Peringatan!",
            text: "Nama wajib diisi !",
            icon: "warning",
            showConfirmButton: false,
            timer: 2000,
        });

        return;
    }

    // disabled.value = true;

    submit(value);
};

onMounted(() => {
    get_json_index();
    if (props.ruangan_choosed !== null) {
        console.log("masuk");
        qrValue();
    }
});

const qrValue = async () => {
    try {
        const response = await axios.post(
            `/ga_sheet/cleaning/get_lokasi_gedung`,
            {
                ruangan: props.ruangan_choosed,
            }
        );
        console.log(response.data);
        (form.value_location = response.data.lokasi_choosed),
            (form.value_gedung = response.data.gedung_choosed),
            (form.value_ruangan = props.ruangan_choosed),
            (form.check_submit = 1);
        form.disabledForm = true;
        changeRuangan(form.value_ruangan);
    } catch (error) {
        console.error(error);
    }
};

// const qrValue = () => {
//   if(props.ruangan_choosed != ''){
//     form.value_location = props.lokasi_choosed,
//     form.value_gedung = props.gedung_choosed,
//     form.value_ruangan = props.ruangan_choosed,
//     form.check_submit = 1
//     form.disabledForm = true;
//     changeRuangan(form.value_ruangan)
//   }else{
//     form.value_ruangan = null
//   }
// }

const get_json_index = async (page = 1, field = "") => {
    // your logic for fetching data
};

const changeLocation = () => {
    form.value_gedung = null;
    form.value_ruangan = null;
};

const changeGedung = () => {
    form.value_ruangan = null;
};

const changeRuangan = async (value) => {
    try {
        const response = await axios.post(
            `/ga_sheet/cleaning/get_alat_simbol_kondisi`,
            {
                value_location: form.value_location,
                value_gedung: form.value_gedung,
                value_ruangan: value,
            }
        );
        if (!value) {
            form.master_alats = [];
        } else {
            form.master_alats = response.data.data_alat;
            form.master_simbol_kondisis = response.data.data_simbol_kondisi;
            form.description = response.data.data_alat.map(() => "");
            form.selected_value = response.data.default_simbol_kondisi
            // console.log(response.data)
        }
    } catch (error) {
        console.error(error);
    }
};

const submit = () => {
    if (form.check_submit == 1) {
        form.value_alat_post = props.alat_choosed;
    } else {
        form.value_alat_post = form.value_alat;
    }
    router.post(
        `/ga_sheet/cleaning`,
        {
            master_alats: form.master_alats,
            simbol_name: form.selected_value,
            is_checked: form.is_checked,
            description: form.description,
            pic: form.selected_pic,
            location: form.value_location,
            gedung: form.value_gedung,
            ruangan: form.value_ruangan,
        },
        {
            onSuccess: () => {
                //show success alert
                swal({
                    title: "Berhasil!",
                    text: "Cleaning berhasil ditambahkan!",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });
            },
            onError: () => {
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

const selectValue = () => {
    for (let i = 0; i < form.master_alats.length; i++) {
        if (!form.selected_value[i]) {
            form.selected_value[i] = { id: null, name: null }; // Ensure a consistent structure
        }
    }
};

const checkAllForm = () => {
    for (let i; i < props.master_alats.length; i++) {
        form.is_checked[i] = true;
    }
};
const allChecklist = () => {
    if (form.allChecklistValue == true) {
        for (let i = 0; i < form.master_alats.length; i++) {
            form.is_checked[i] = false;
        }
    } else {
        for (let i = 0; i < form.master_alats.length; i++) {
            form.is_checked[i] = true;
        }
    }
};
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Cleaning Sheet" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFileDocumentPlusOutline"
                title="Tambah Cleaning"
                main
            >
                <BaseButton
                    :route-name="route('cleaning.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
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
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
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
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="Ruangan" :required="true">
                        <Multiselect
                            v-model="form.value_ruangan"
                            :options="filteredRuangan"
                            placeholder="Pilih Ruangan"
                            @update:modelValue="changeRuangan"
                            label="name"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="PIC" :required="true">
                        <Multiselect
                            v-model="form.selected_pic"
                            :options="props.master_pics"
                            placeholder="Pilih PIC"
                            label="pic_name"
                            track-by="id"
                        />
                    </FormField>
                </div>
                <div
                    class="sm:invisible w-full flex justify-end p-4"
                    v-if="form.master_alats.length > 0"
                >
                    <input
                        name="data.form.checkbox"
                        type="checkbox"
                        v-model="form.allChecklistValue"
                        @click="allChecklist(form.allChecklistValue)"
                    />
                    <span class="ml-3 -mt-1">Check All</span>
                </div>
            </div>
            <CardBox class="mb-6" has-table v-if="form.master_alats.length > 0">
                <div class="overflow-x-auto">
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
                                        class="flex items-center justify-center gap-3 cursor-pointer m-1"
                                    >
                                        <span
                                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                                            >Check All</span
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
                                v-for="(data, id) in form.master_alats"
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