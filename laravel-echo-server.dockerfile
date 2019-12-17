FROM node:12.13.0-alpine

RUN sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories

RUN apk update && \
    apk add --update \
    python \
    python-dev \
    py-pip \
    build-base

RUN npm config set registry https://registry.npm.taobao.org && \
npm install -g yarn && \
yarn config set registry https://registry.npm.taobao.org -g && \
yarn config set sass_binary_site http://cdn.npm.taobao.org/dist/node-sass -g && \
yarn global add laravel-echo-server@^1.5.9 

CMD [ "yarn", "start"]
