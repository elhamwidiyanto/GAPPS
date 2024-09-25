<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from "vue";
import { router, Head, Link, useForm } from "@inertiajs/vue3";
import {
    mdiAccountKey,
    mdiArrowLeftBoldOutline,
    mdiFileDocumentEdit,
    mdiAccountSwitch,
    mdiTrashCanOutline,
    mdiClipboardListOutline,
    mdiStateMachine,
    mdiAccountArrowUp,
    mdiFileDocumentPlus,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
// import FormFilePicker from "@/components/FormFilePicker.vue";
// import BaseDivider from "@/components/BaseDivider.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "vue-multiselect";
import moment from "moment";
import useNumberFormat from "../../../Helpers/numberFormat";

//import axios
import axios from "axios";

const props = defineProps({
    app_url: {
        type: String,
        default: () => "",
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
    master_alats: {
        type: Array,
        default: () => [],
    },
    master_types: {
        type: Array,
        default: () => [],
    },
});

const { numberFormat } = useNumberFormat();

onMounted(() => {});

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
};

const swal = inject("$swal");
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
    name: "",
    is_active: false,
    value_gedung: [],
    value_location: "",
    value_ruangan: "",
    value_alat: "",
    type: "",
    selected_alat:[],
    alats: [],
    value_default_simbol: "",
    simbols: [],
});

const delay = async (value) => {
    if (form.name == "" || form.name == null) {
        swal({
            title: "Peringatan!",
            text: "Nama wajib diisi !",
            icon: "warning",
            showConfirmButton: false,
            timer: 2000,
        });

        return;
    }

    disabled.value = true;

    // buttonLoading.value = true;

    // // Re-enable after 5 seconds
    // timeout.value = setTimeout(() => {
    //     disabled.value = false;
    //     buttonLoading.value = false;
    // }, 20000)

    submit(value);
};

// const getRuangan = async (value) => {
//   try {
//     const response = await axios.post(`/ga_sheet/maintenance/get_ruangan`, {
//       value_location: form.value_location,
//       value_gedung: form.value_gedung,
//     })
//     if (!value) {
//       form.master_ruangans = []
//     } else {
//       form.master_ruangans = response.master_ruangans
//     }
//   } catch (error) {
//     console.error(error)
//   }
// }
const filteredRuangan = computed(() => {
    console.log(props.master_alats);
    if (form.value_gedung) {
        return props.master_ruangans.filter(
            (ruangan) => ruangan.id_gedung === form.value_gedung.id
        );
    }
    return [];
});

const filteredAlat = computed(() => {
    console.log(props.master_ruangans);
    if (form.value_ruangan) {
        return props.master_alats.filter(
            (alat) => alat.ruangan_id === form.value_ruangan.id
        );
    }
    return [];
});

//method "submit"
const submit = (value) => {
    console.log(form.value_default_simbol)
    router.post(
        `/ga_sheet/komponen`,
        {
            name: form.name,
            alat_id: form.selected_alat.id,
            type_id: form.type.id,
            type_name: form.type.name,
            is_active: form.is_active,
            default_simbol: form.value_default_simbol.id,
        },
        {
            onSuccess: () => {
                //show success alert
                swal({
                    title: "Berhasil!",
                    text: "Komponen berhasil ditambahkan!",
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
    form.value_gedung = "";
    form.value_ruangan = "";

    if (form.value_location) {
        return props.master_gedungs.filter(
            (gedung) => gedung.id_lokasi === form.value_location.id
        );
    }
    return [];
});

const changeLocation = () => {
    form.value_gedung = "";
    form.value_ruangan = "";
    form.value_alat = "";
};

const changeGedung = () => {
    form.value_ruangan = "";
    form.value_alat = "";
};

const changeRuangan = () => {
    form.value_alat = "";
};
const get_alat = async(value) => {
    form.selected_alat = null
    if(value){
        try {
        const response = await axios.post(
            `/ga_sheet/simbol_kondisi/get_alat`,
            {
                sheet: value,
            }
            
        );
        form.alats = response.data.alats
        } catch (error) {
            console.error(error);
        }
    }else{
        form.selected_alat = null
        form.alats = []

    }
}
const formatLabel= (option) =>{
    if(form.type.id == 1){
        return `${option.name}`;

    }else{
        return `${option.name} - ${option.serial_number}`;
    }

    }
const getDefaultSimbol = async(valueAlat) => {
  if(valueAlat){
        try {
        const response = await axios.post(
            `/ga_sheet/komponen/get_default_simbol`,
            {
                alat: valueAlat,
            }
            
        );
        form.simbols = response.data.simbol_kondisis
        } catch (error) {
            console.error(error);
        }
      }
}
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Komponen" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFileDocumentPlus"
                title="Tambah Komponen"
                main
            >
                <BaseButton
                    :route-name="route('komponen.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <CardBox>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Name" :required="true">
                            <input
                                type="text"
                                placeholder="Input nama..."
                                class="dy-input dy-input-bordered dy-input-info w-full"
                                v-model="form.name"
                            />
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="typo__label"></label>
                            <FormField label="Pilih Sheet" :required="true">
                                <multiselect
                                    v-model="form.type"
                                    :options="props.master_types"
                                    label="name"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="get_alat"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Alat" :required="true">
                                <multiselect
                                    v-model="form.selected_alat"
                                    :options="form.alats"
                                    :custom-label="formatLabel"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="getDefaultSimbol"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Kondisi" :required="true">
                                <multiselect
                                    v-model="form.value_default_simbol"
                                    :options="form.simbols"
                                    label="name"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                    </div>
                </div>

                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Status" :required="true">
                            <FormCheckRadioGroup
                                v-model="form.is_active"
                                type="switch"
                                name="notifications-switch"
                                :options="{ outline: '' }"
                                class="mb-2"
                            />
                        </FormField>
                    </div>
                </div>
                <template #footer>
                    <BaseButtons class="flex justify-end">
                        <BaseButton
                            type="submit"
                            color="success"
                            label="Simpan"
                            :class="{ 'opacity-25': disabled }"
                            :disabled="disabled"
                            @click="delay(1)"
                        />
                    </BaseButtons>
                </template>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
