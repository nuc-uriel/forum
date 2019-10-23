FROM node:12.13.0-alpine

# Create app directory
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# Install app dependencies
COPY package.json /usr/src/app/



RUN apk update && \
	apk add --update \
    python \
    python-dev \
    py-pip \
    build-base

RUN npm config set registry http://registry.npm.taobao.org/

RUN npm install -g laravel-echo-server

# Bundle app source
COPY laravel-echo-server.json /usr/src/app/laravel-echo-server.json

EXPOSE 3000
CMD [ "npm", "start", "--force" ]