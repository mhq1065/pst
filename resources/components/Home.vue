<template>
    <div id="app">
        <button type="button" class="btn btn-primary" @click="showUploadModal">
            上传
        </button>
        <!-- 文件列表 -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>文件名称</th>
                    <th>大小</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in FileList" :key="index">
                    <td>{{ item.fileName }}</td>
                    <td>{{ item.size }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn"
                            @click="showDownModal(index)"
                        >
                            下载
                        </button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger">
                            删除
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- 弹窗 -->
        <div class="modal" tabindex="1" role="dialog" :style="Modal.ModalStyle">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ Modal.type }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                            @click="hideModal"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <!-- 下载 -->
                    <div class="modal-body" v-if="Modal.type === '下载'">
                        <p>文件名：{{ Modal.FileName }}</p>
                        <p>大小：{{ Modal.size }}</p>
                        <input
                            type="password"
                            class="form-control"
                            v-model="Modal.downpwd"
                            placeholder="下载/删除口令"
                        />
                    </div>
                    <!-- 上传 -->
                    <div class="modal-body" v-else>
                        <!-- 文件选择 -->
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input
                                    type="file"
                                    class="custom-file-input"
                                    id="inputFile"
                                    @change="handleFileChange($event)"
                                />
                                <label
                                    class="custom-file-label"
                                    for="inputFile"
                                >
                                    {{ fileName }}
                                </label>
                            </div>
                            <div class="input-group-append">
                                <span
                                    class="input-group-text"
                                    @click.prevent="uploadFile"
                                >
                                    上传
                                </span>
                            </div>
                        </div>
                        <!-- 新文件名 -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text"
                                    id="inputGroup-sizing-default"
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
                        <!-- 密码输入 -->
                        <div class="form-group">
                            <label for="exampleInputPassword1">
                                口令
                                <small>不设置密码任何人均可下载删除</small>
                            </label>
                            <input
                                type="password"
                                class="form-control"
                                v-model="pwd"
                                placeholder="下载/删除口令"
                            />
                        </div>
                    </div>
                    <!-- Modal foot -->
                    <div class="modal-footer">
                        <!-- 下载 -->
                        <button
                            v-if="Modal.type == '下载'"
                            type="button"
                            class="btn btn-primary"
                            @click.prevent="downloadFile"
                        >
                            下载
                        </button>
                        <!-- 上传 -->
                        <button
                            v-else
                            type="button"
                            class="btn btn-primary"
                            @click.prevent="uploadFile"
                        >
                            上传
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 遮罩层 -->
        <div
            class="overlay"
            v-show="Modal.ModalStyle.display !== 'none'"
            @click="hideModal"
        ></div>
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
            FileList: [],
            fileName: "Choose file",
            newFileName: "",
            file: {},
            pwd: "",
            Modal: {
                type: "上传",
                downpwd: "",
                id: 0,
                FileName: "",
                size: 0,
                ModalStyle: {
                    display: "none"
                }
            }
        };
    },
    // 加载文件列表
    mounted() {
        console.log("mounted");
        this.freshList();
    },
    methods: {
        // 刷新文件列表
        freshList() {
            console.log("fresh the file list");
            this.$ajax({
                method: "get",
                url: "/files"
            }).then(res => {
                this.FileList = res.data;
            });
        },
        // 选择上传文件，加载参数
        handleFileChange(event) {
            console.log("选择上传文件", event.target.files);
            this.file = event.target.files[0];
            this.fileName = this.file.name;
            this.newFileName = this.file.name;
        },
        // 上传文件
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
            }).then(res => {
                this.freshList();
                this.hideModal();
            });
        },
        // 显示=弹窗
        showModal() {
            this.Modal.ModalStyle.display = "inherit";
            this.fixedBody();
        },
        // 隐藏弹窗
        hideModal() {
            console.log("hide Modal");
            this.Modal.ModalStyle.display = "none";
            this.clearModal();
            this.looseBody();
        },
        clearModal() {
            this.Modal = {
                type: "下载",
                id: 0,
                FileName: "",
                downpwd: "",
                size: 0,
                ModalStyle: {
                    display: "none"
                }
            };
        },
        // 下载
        downloadFile() {
            this.$ajax({
                url: `download/${this.Modal.id}`,
                method: "post",
                data: {
                    pwd: this.Modal.downpwd
                },
                responseType: "blob"
            }).then(res => {
                if (res.data.retcode === -1) {
                    alert("密码错误");
                }
                let blob = new Blob([res.data], {
                    type: "text/plain;charset=UTF-8"
                });
                let objectUrl = URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = objectUrl;
                a.download = this.Modal.FileName; // 这里的文件名可以去res的header中取
                a.click();
                // 释放url对象
                window.URL.revokeObjectURL(objectUrl);
                this.clearModal();
                // window.open(res.data.url);
            });
        },

        //防止滚动穿透
        //打开模态框前调用
        fixedBody() {
            var body = document.body;
            body.className = "preventScroll";
        },
        //关闭模态框后调用
        looseBody() {
            var body = document.body;
            body.className = "";
        },

        showUploadModal() {
            this.showModal();
            this.Modal.type = "上传";
        },
        // 显示下载弹窗
        showDownModal(id) {
            this.showModal();
            this.Modal.type = "下载";
            this.Modal.FileName = this.FileList[id].fileName;
            this.Modal.size = this.FileList[id].size;
            this.Modal.id = this.FileList[id].id;
        }
    }
};
</script>
