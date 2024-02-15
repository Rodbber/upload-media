<template>
  <div>
    <div>
      <label for="">New file</label>
      <input
        @input="setFile"
        multiple
        type="file"
        accept=".jpg,.png,.gif,.mp3,.wav"
      />
      <div>
        <button @click="saveFile">Save</button>
      </div>
    </div>
    <div>
      <div class="flex" v-for="(v, index) in list" :key="v.id">
        <div>
          <input v-model="list[index].file_name" type="text" />
        </div>
        <div>
          <span>{{ v.extension }}</span>
        </div>
        <div>
          <button @click="renameFile(index)">Save</button>
        </div>
        <div>
          <button @click="copyFullUrl(index)">Copy url</button>
        </div>
        <div>
          <button @click="deleteFile(v.id)">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.flex {
    display: flex;
    flex-direction: row;
}

.flex div {
    margin-left: 10px;
    margin-right: 10px;
}

</style>

<script lang="ts">
import { defineComponent } from "vue";
import axios from "axios";

export default defineComponent({
  data() {
    return {
      baseUrl: "http://localhost:8000",
      list: [],
      files: [],
      newFileName: null,
    };
  },
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
    renameFile(index: number) {
      axios
        .post(
          "/media-files/" + this.list[index].id,
          { file_name: this.list[index].file_name },
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((r) => {
          console.log(r.data);
        })
        .catch((e) => {
          console.log(e);
        });
    },
    deleteFile(id: number) {
      axios
        .delete("/media-files/" + id)
        .then((r) => {
          console.log(r.data);
          this.getData();
        })
        .catch((e) => {
          console.log(e);
        });
    },
    setFile(e) {
      this.files = e.target.files;
    },
    saveFile() {
      const formData = new FormData();
      for (let i of this.files) {
        formData.append("files[]", i);
      }
      axios
        .post("/media-files", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((r) => {
          console.log(r.data);
          this.getData();
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getData() {
      axios
        .get("/media-files")
        .then((r) => {
          console.log(r.data);
          this.list = r.data;
        })
        .catch((e) => {
          console.log(e);
        });
    },
  },
  created() {
    this.getData();
  },
});
</script>
