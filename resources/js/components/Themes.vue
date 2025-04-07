<!--suppress XmlInvalidId, XmlDuplicatedId, CssInvalidPseudoSelector, CssUnusedSymbol -->
<template>
    <div>
        <section id="ClientList" class="openTicket">

            <div class="container custom_width">

                <!-- Header -->
                <div class="row">

                    <div class="col-lg-12">

                        <div class="FilterBy d_flex justify-content-between">

                            <div class="d-flex">
                                <!-- Filter By Item -->
                                <div class="FilterBy_item d_flex">
                                    <h3>Filter By:</h3>
                                    <div class="dropdown_part">
                                        <span class="dropdown-toggle d_flex" id="dropdownMenuButton1"
                                              data-bs-toggle="dropdown"
                                              aria-expanded="false">
                                            Joining Date
                                            <span class="arrow">
                                                <svg width="11" height="6" viewBox="0 0 11 6" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.244629 0.501221L5.40989 5.66649L10.5752 0.501221H0.244629Z"
                                                        fill="#747474"/>
                                                </svg>
                                            </span>
                                        </span>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>

                                            <!-- up arrow -->
                                            <div class="up_arrow">
                                                <svg width="11" height="6" viewBox="0 0 11 6" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10.3306 5.16528L5.1653 1.6953e-05L3.48091e-05 5.16528L10.3306 5.16528Z"
                                                        fill="#F3ECFF"/>
                                                </svg>
                                            </div>

                                        </ul>

                                    </div>

                                </div>

                                <!-- Filter By Item -->
                                <div class="FilterBy_item d_flex">
                                    <h3>Filter By:</h3>
                                    <div class="dropdown_part">
                                        <span class="dropdown-toggle d_flex" id="dropdownMenuButton1"
                                              data-bs-toggle="dropdown"
                                              aria-expanded="false">
                                            Status
                                            <div class="arrow">
                                                <svg width="11" height="6" viewBox="0 0 11 6" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.244629 0.501221L5.40989 5.66649L10.5752 0.501221H0.244629Z"
                                                        fill="#747474"/>
                                                </svg>
                                            </div>
                                        </span>

                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>

                                            <!-- up arrow -->
                                            <div class="up_arrow">
                                                <svg width="11" height="6" viewBox="0 0 11 6" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10.3306 5.16528L5.1653 1.6953e-05L3.48091e-05 5.16528L10.3306 5.16528Z"
                                                        fill="#F3ECFF"/>
                                                </svg>
                                            </div>

                                        </ul>

                                    </div>

                                </div>

                                <!-- Filter By Item -->
                                <div class="FilterBy_item">
                                    <div class="custome_input">

                                        <label class="mb-0">
                                            <input type="text" name="" placeholder="Search here..."/>
                                        </label>

                                        <div class="search">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M16.3078 16.7113L12.6943 13.0914M14.6968 8.25366C14.6968 10.0695 13.9754 11.811 12.6914 13.095C11.4074 14.379 9.66595 15.1003 7.8501 15.1003C6.03425 15.1003 4.29277 14.379 3.00876 13.095C1.72476 11.811 1.00342 10.0695 1.00342 8.25366C1.00342 6.43781 1.72476 4.69633 3.00876 3.41233C4.29277 2.12833 6.03425 1.40698 7.8501 1.40698C9.66595 1.40698 11.4074 2.12833 12.6914 3.41233C13.9754 4.69633 14.6968 6.43781 14.6968 8.25366V8.25366Z"
                                                    stroke="#A3A3A3" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="justify-content-end">
                                <a @click="[showModal = true, errors = {}]" class="btn-default cursor">Add New</a>
                            </div>

                        </div>

                    </div>

                    <!-- Table Part  -->

                    <div class="col-lg-12" v-if="themes.length > 0">
                        <div class="row">
                            <div class="col-lg-3" v-for="theme in themes" :key="theme.id">
                                <div class="bg-white border rounded-2 p-4 mb-4">
                                    <div style="text-align: center">
                                        <span>{{ theme?.theme_name }}</span>
                                    </div>
                                    <div class="template-image">
                                        <img :src="theme?.media?.name" alt="">
                                    </div>
                                    <div class="template-info">

                                        <div>
                                            <a href="" class="me-1"><i class="fa fa-pencil-alt"></i></a>
                                            <a href="" class="ms-1"><i class="fa fa-trash-alt"></i></a>
                                        </div>

                                        <a class="" :href="theme?.url" target="_blank">Preview</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" v-else>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white border rounded-2 p-2 text-center">
                                    No Data found
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </section>

        <form class="mt-4" @submit.prevent="handleSubmit" enctype="multipart/form-data">
            <Modal :show="showModal" @close="showModal = false">
                <template #header>
                    <h6>Add New Template</h6>
                </template>

                <template #default>

                    <div class="form-group mb-2 mt-4">
                        <label for="merchants" :class="['mb-0', !!errors.type && 'validation-error-label']">Template
                            Type *</label>

                        <select :class="[ 'form-control', !!errors.type && 'validation-error' ]" v-model="form.type">
                            <option disabled value="">Please select Template type</option>
                            <option :value="item.value" v-for="item in types" :key="item.id">{{ item.name }}</option>
                        </select>

                        <span class="validation-error-message" v-if="!!errors.type">{{ errors.type }}</span>

                    </div>

                    <div class="form-group mb-2">
                        <label for="subject" :class="['mb-0', !!errors.name && 'validation-error-label']">Title
                            *</label>

                        <input type="text" v-model="form.name" name="subject"
                               :class="[ 'form-control', !!errors.name && 'validation-error' ]"
                               @blur="validate('name')"
                               @keypress="validate('name')"/>

                        <span class="validation-error-message" v-if="!!errors.name">{{ errors.name }}</span>
                    </div>

                    <div class="form-group mb-2">
                        <label for="subject" :class="['mb-0', !!errors.url && 'validation-error-label']">Preview Url
                            *</label>

                        <input type="text" v-model="form.url" name="subject"
                               :class="[ 'form-control', !!errors.url && 'validation-error' ]"
                               @blur="validate('url')"
                               @keypress="validate('url')"/>

                        <span class="validation-error-message" v-if="!!errors.url">{{ errors.url }}</span>
                    </div>

                    <div class="form-group mb-2">
                        <label for="" :class="['mb-0', !!errors.image && 'validation-error-label']">Preview Image
                            *</label>
                        <input type="file" name="attachment"
                               :class="[ 'file-control', !!errors.image && 'validation-error' ]"
                               @change="fileUpload"/>
                        <span class="validation-error-message" v-if="!!errors.image">{{ errors.image }}</span>
                    </div>

                </template>
            </Modal>
        </form>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import * as yup from 'yup';

const themeSchema = yup.object().shape({
    name: yup.string().required('title is a required field'),
    image: yup.mixed().test("type", 'Supported file types Image only', function (value) {
        if (value !== null) {
            return value && (value.type === 'image/jpg' || value.type === 'image/jpeg' || value.type === 'image/png');
        } else {
            return false;
        }

    }),
    type: yup.string().required('Please Select Template Type'),
    url: yup.string().required()
});

export default {
    name: "Themes",
    components: {Modal},
    data() {
        return {
            showModal: false,
            form: {
                type: "",
                name: "",
                image: null,
                url: "",
            },
            errors: {
                type: "",
                name: "",
                image: "",
                url: ""
            },
            types: [
                {name: 'Landing', value: 'landing'},
                {name: 'Multipage', value: 'multiple'}
            ],
            themes: []
        }
    },
    methods: {
        validate(field) {
            themeSchema
                .validateAt(field, this.form)
                .then(() => {
                    this.errors[field] = "";
                })
                .catch(err => {
                    this.errors[field] = err.message;
                })
        },
        handleSubmit() {
            themeSchema
                .validate(this.form, {abortEarly: false})
                .then(() => {
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('type', this.form.type)
                    formData.append('name', this.form.name)
                    formData.append('url', this.form.url)
                    formData.append('image', this.form.image)

                    axios.post('/panel/themes/store', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }).then((response) => {
                        this.showModal = false
                        this.form.type = ""
                        this.form.name = ""
                        this.form.url = ""
                        this.form.image = null
                        this.fetchThemes()

                    }).catch(error => {
                        console.log('validation', error)
                    })
                })
                .catch(err => {
                    err.inner.forEach(error => {
                        this.errors[error.path] = error.message;
                    });
                })
        },
        fileUpload(event) {
            this.form.image = event.target["files"][0];
            this.validate('image')
        },
        fetchThemes() {
            axios.get('/panel/themes/list').then((res) => {
                this.themes = res.data.data
            })
        }
    },
    mounted() {
        this.fetchThemes()
    }
}
</script>

<style scoped>
.template-image {
    width: 100%;
    align-items: center;
}

.template-image img {
    width: 100%;
    height: auto;
    max-height: 320px;
}

.template-info {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.template-info button {
    width: 100%;
}

.cursor {
    cursor: pointer !important;
}

.file-control::file-selector-button {
    background: #fff;
    color: #000;
    border: 0px;
    border-right: 1px solid #e5e5e5;
    padding: 0px 15px;
    margin-right: 20px;
    transition: .5s;
}
</style>
