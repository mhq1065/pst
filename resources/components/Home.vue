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
                        <button
                            type="button"
                            class="btn btn-danger"
                            @click="showDeleteModal(index)"
                        >
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
                            v-model="Modal.pwd"
                            placeholder="下载/删除口令"
                        />
                    </div>
                    <!-- 上传 -->
                    <div class="modal-body" v-else-if="Modal.type == '上传'">
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
                                v-model="Modal.pwd"
                                placeholder="下载/删除口令"
                            />
                        </div>
                        <!-- 进度条 -->
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                :style="Modal.uploadProgress">
                            </div>
                        </div>
                    </div>
                    <!-- 删除 -->
                    <div class="modal-body" v-else>
                        <p>文件名：{{ Modal.FileName }}</p>
                        <p>大小：{{ Modal.size }}</p>
                        <input
                            type="password"
                            class="form-control"
                            v-model="Modal.pwd"
                            placeholder="下载/删除口令"
                        />
                        <small>删除操作不可逆，请谨慎操作</small>
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
                            v-else-if="Modal.type == '上传'"
                            type="button"
                            class="btn btn-primary"
                            @click.prevent="uploadFile"
                        >
                            上传
                        </button>
                        <!-- 删除 -->
                        <button
                            v-else
                            type="button"
                            class="btn btn-danger"
                            @click.prevent="deleteFile"
                        >
                            删除
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
            Modal: {
                type: "上传",
                pwd: "",
                id: 0,
                FileName: "",
                size: 0,
                ModalStyle: {
                    display: "none"
                },
                uploadProgress:{
                    width:'0%'
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
            formdata.append("pwd", this.Modal.pwd);
            formdata.append("file", this.file);
            formdata.append("newfilename", this.newFileName);
            if(this.file.size>20971500){
                alert("请选择小于20M的文件")
                return;
            }
            // console.log(formdata);
            this.$ajax({
                method: "post",
                url: "/upload",
                data: formdata,
                headers: {
                    "Content-Type": "multipart/form-data;charset=UTF-8"
                },
                onUploadProgress: progressEvent => {
                    let complete = (progressEvent.loaded / progressEvent.total * 100 | 0) + '%'
                    this.Modal.uploadProgress.width = complete
                    console.log(this.Modal.uploadProgress.width)
                }
            }).then(res => {
                alert("上传成功");
                this.clearFile();
                this.freshList();
                this.hideModal();
            }).catch(err=>{
                console.log('err',err);
                console.log('error code',err.response.status);
                this.clearFile()
                if(err.response.status==413){
                    alert("上传失败,文件过大");
                }else{
                    alrt("上传失败，请检查网络连接")
                }
            });
        },
        // 下载文件
        downloadFile() {
            this.$ajax({
                url: `download/${this.Modal.id}`,
                method: "post",
                data: {
                    pwd: this.Modal.pwd
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
            }).catch(err=>{
                console.log(err);
                alert("密码错误")
            });
        },
        // 删除文件
        deleteFile() {
            this.$ajax({
                method: "delete",
                url: `/files/${this.Modal.id}`,
                data: {
                    pwd: this.Modal.pwd
                }
            }).then(res => {
                alert("删除成功");
                this.freshList();
                this.hideModal();
            });
        },

        // 显示弹窗
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
        // 清空Modal
        clearModal() {
            this.Modal = {
                type: "下载",
                id: 0,
                FileName: "",
                pwd: "",
                size: 0,
                ModalStyle: {
                    display: "none"
                },
                uploadProgress:{
                    width:"0"
                }
            };
        },
        // 清空文件
        clearFile(){
            this.fileName="Choose file";
            this.newFileName= "";
            this.file= {};
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
        //  显示上传弹窗
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
        },
        // 显示删除弹窗
        showDeleteModal(id) {
            this.showModal();
            this.Modal.type = "删除";
            this.Modal.FileName = this.FileList[id].fileName;
            this.Modal.size = this.FileList[id].size;
            this.Modal.id = this.FileList[id].id;
        }
    }
};
</script>
