<template>
    <div id="app">
        <div class="input-group mb-3">
            <div class="custom-file">
                <input
                    type="file"
                    class="custom-file-input"
                    id="inputFile"
                    @change="handleFileChange($event)"
                />
                <label class="custom-file-label" for="inputFile">
                    {{ fileName }}
                </label>
            </div>
            <div class="input-group-append">
                <span class="input-group-text" @click.prevent="uploadFile">
                    上传
                </span>
            </div>
        </div>
        <!-- 新文件名 -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inputGroup-sizing-default"
                    >上传文件名</span
                >
            </div>
            <input
                type="text"
                class="form-control"
                aria-label="Default"
                aria-describedby="inputGroup-sizing-default"
                v-model="newFileName"
            />
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">口令</label>
            <input
                type="password"
                class="form-control"
                v-model="pwd"
                placeholder="下载/删除口令"
            />
        </div>
    </div>
</template>
<script>
export default {
    name: "app",
    data() {
        return {
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            fileName: "Choose file",
            newFileName: "",
            file: {},
            pwd: ""
        };
    },
    methods: {
        handleFileChange(event) {
            console.log("选择上传文件", event.target.files);
            this.file = event.target.files[0];
            this.fileName = this.file.name;
            this.newFileName = this.file.name;
        },
        uploadFile() {
            console.log("start uploadFile");
            let formdata = new FormData();
            formdata.append("pwd", this.pwd);
            formdata.append("file", this.file);
            formdata.append("newfilename", this.newFileName);
            // console.log(formdata);
            this.$ajax({
                method: "post",
                url: "/upload",
                data: formdata,
                headers: {
                    "Content-Type": "multipart/form-data;charset=UTF-8"
                }
            });
        }
    }
};
</script>
