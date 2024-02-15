<template>
  <div>
    <v-row justify="end">
      <v-col cols="10">
        <v-file-input
          v-model="files"
          color="deep-purple-accent-4"
          counter
          label="File input"
          multiple
          placeholder="Select your files"
          prepend-icon="mdi-paperclip"
          variant="outlined"
          :show-size="1000"
          accept=".jpg,.png,.gif,.mp3,.wav"
        >
          <template v-slot:selection="{ fileNames }">
            <template v-for="(fileName, index) in fileNames" :key="fileName">
              <v-chip
                v-if="index < 2"
                color="deep-purple-accent-4"
                label
                size="small"
                class="me-2"
              >
                {{ fileName }}
              </v-chip>

              <span
                v-else-if="index === 2"
                class="text-overline text-grey-darken-3 mx-2"
              >
                +{{ files.length - 2 }} File(s)
              </span>
            </template>
          </template>
        </v-file-input>
      </v-col>
      <v-col justify="center">
        <v-btn
          :loading="sendLoading"
          class="flex-grow-1"
          height="48"
          variant="tonal"
          @click="saveFile"
        >
          Save
        </v-btn>
      </v-col>
    </v-row>
    <v-row justify="end" class="mb-5">
      <v-col cols="5">
        <v-text-field
          density="compact"
          variant="solo"
          label="Search"
          append-inner-icon="mdi-magnify"
          single-line
          hide-details
          v-model="search"
          @input="searchFile"
        ></v-text-field>
      </v-col>
    </v-row>
    <v-data-table
      v-model:items-per-page="itemsPerPage"
      :search="search"
      :headers="headers"
      :items-length="totalItems"
      :items="serverItems"
      :loading="loading"
      item-value="file_name"
      @update:options="loadItems"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon
          size="small"
          class="me-2"
          @click="openModalChangeFileName(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon size="small" @click="openModalExclude(item)">
          mdi-delete
        </v-icon>
      </template>
    </v-data-table>
    <modal-vue
      @close="closeModalExclude"
      :is-active="modalActiveExclude"
      @confirm="deleteFile"
    >
      <template #modal-body>
        <div>
          <span
            >Do you confirm that you want to remove
            {{ selectedItemExclude.file_name }}?</span
          >
        </div>
      </template>
    </modal-vue>
    <modal-vue
      @close="closeModalRename"
      :is-active="modalActiveRename"
      @confirm="renameFile"
    >
      <template #modal-body>
        <div>
          <v-col cols="12">
            <v-text-field
              v-model="selectedItemRename.file_name"
              label="Outlined"
              variant="outlined"
            ></v-text-field>
          </v-col>
        </div>
      </template>
    </modal-vue>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import axios from "axios";
import ModalVue from "./Modal.vue";

export default defineComponent({
  components: { ModalVue },
  data: () => ({
    // modal exclude
    modalActiveExclude: false,
    selectedItemExclude: null,
    // modal rename
    modalActiveRename: false,
    selectedItemRename: null,
    // files
    files: [],
    sendLoading: false,

    itemsPerPage: 5,
    headers: [
      {
        title: "File name",
        align: "center",
        sortable: true,
        key: "file_name",
      },
      { title: "Extension", key: "extension", align: "center", sortable: true },
      { title: "Updated", key: "updated_at", align: "center", sortable: true },
      { title: "Actions", key: "actions", sortable: false },
    ],
    serverItems: [],
    loading: true,
    totalItems: 0,
    name: "",
    calories: "",
    search: "",
  }),
  methods: {
    copyFullUrl(index: number) {
      const text = this.baseUrl + this.list[index].full_url;
      var input = document.createElement("input");
      input.setAttribute("value", text);
      document.body.appendChild(input);
      input.select();
      input.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(input.value);
      document.body.removeChild(input);
    },
    openModalChangeFileName(item: any) {
      this.selectedItemRename = { ...item };
      this.modalActiveRename = true;
    },
    closeModalRename() {
      this.modalActiveRename = false;
    },
    openModalExclude(item: any) {
      this.selectedItemExclude = item;
      this.modalActiveExclude = true;
    },
    closeModalExclude() {
      this.modalActiveExclude = false;
    },
    renameFile(index: number) {
      axios
        .post(
          "/media-files/" + this.selectedItemRename.id,
          { file_name: this.selectedItemRename.file_name },
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((r) => {
          this.loadItems({ page: this.page, itemsPerPage: this.itemsPerPage });
        })
        .catch((e) => {
          console.log(e);
        });
    },
    deleteFile() {
      axios
        .delete("/media-files/" + this.selectedItemExclude.id)
        .then((r) => {
          this.loadItems({ page: this.page, itemsPerPage: this.itemsPerPage });
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getData({ page = null, itemsPerPage = null, sortBy = null }) {
      return axios.get("/media-files", {
        params: {
          page,
          itemsPerPage,
          sortBy,
          search: this.search,
        },
      });
    },
    loadItems({ page, itemsPerPage, sortBy }) {
      this.loading = true;
      this.getData({ page, itemsPerPage, sortBy, search: this.search }).then(
        ({ data: { data: items, total } }) => {
          this.serverItems = items;
          this.totalItems = total;
          this.loading = false;
        }
      );
    },
    setFile(e) {
      this.files = e.target.files;
    },
    saveFile() {
      const formData = new FormData();
      for (let i of this.files) {
        formData.append("files[]", i);
      }
      this.sendLoading = true;
      axios
        .post("/media-files", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((r) => {
          this.loadItems({ page: this.page, itemsPerPage: this.itemsPerPage });
          this.getData();
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => (this.sendLoading = false));
    },
  },
});
</script>
