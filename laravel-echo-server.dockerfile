FROM node:12.13.0-alpine

RUN sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories

# Create app directory
WORKDIR /usr/src/app

# Install app dependencies
COPY package.json /usr/src/app/

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
yarn

# Bundle app source
COPY laravel-echo-server.json /usr/src/app/laravel-echo-server.json

EXPOSE 6001
CMD [ "yarn", "start"]