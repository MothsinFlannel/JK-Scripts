<template>
  <div class="default-layout">
    <b-navbar
      :sticky="true"
      type="light"
      variant="white"
      toggleable=""
      class="px-4"
    >
      <b-navbar-brand to="/" class="text-primary default-layout__logo">
        vCard
      </b-navbar-brand>
      <div v-if="$isAllowed('app/companies/list')" class="ml-auto">
        <CompanySelector />
      </div>
      <div class="d-flex align-items-center ml-3">
        <b-dropdown
          v-if="user"
          variant="link"
          toggle-class="text-decoration-none px-0"
          menu-class="p-0 overflow-hidden"
          no-caret
          right
        >
          <template #button-content>
            <b-avatar variant="primary" size="2.125rem" />
          </template>

          <b-dropdown-group class="py-2 bg-primary text-white">
            <b-dropdown-text style="width: 15rem;">
              <span>{{ user.fullName }}</span>
              <span class="small text-capitalize">{{ user.role }}</span>
            </b-dropdown-text>
          </b-dropdown-group>
          <b-dropdown-item to="/account">Account</b-dropdown-item>
          <b-dropdown-item @click="confirmLogout">Logout</b-dropdown-item>
        </b-dropdown>

        <b-navbar-toggle target="nav-collapse" class="ml-3" />
      </div>

      <b-collapse
        id="nav-collapse"
        v-model="isVisible"
        :class="{
          'mobile-menu_open': isVisible,
        }"
        class="mobile-menu"
        is-nav
      >
        <b-navbar-nav>
          <b-nav-text>Menu</b-nav-text>

          <b-nav-item to="/" class="ml-3">
            Admin Dashboard
          </b-nav-item>

          <template v-if="$isAllowed('app/locations/list')">
            <b-nav-text class="ml-3">Locations</b-nav-text>

            <b-nav-item to="/locations/all" style="margin-left: 2rem;">
              All Locations
            </b-nav-item>

            <b-nav-item to="/locations/offline" style="margin-left: 2rem;">
              Offline Locations
            </b-nav-item>
          </template>

          <template v-if="$isAllowed('app/terminals/list')">
            <b-nav-text class="ml-3">Terminals</b-nav-text>

            <b-nav-item to="/terminals/all" style="margin-left: 2rem;">
              Machines
            </b-nav-item>

            <b-nav-item to="/terminals/offline" style="margin-left: 2rem;">
              Offline Terminals
            </b-nav-item>
          </template>

          <template v-if="false && $isAllowed('app/boards/list')">
            <b-nav-text class="d-flex align-items-center ml-3">
              Boards

              <div class="ml-auto">
                <b-dropdown
                  boundary="window"
                  size="sm"
                  variant="link"
                  class="nav-dropdown"
                  toggle-class="text-decoration-none"
                  menu-class="position-absolute"
                  dropleft
                  no-caret
                >
                  <template #button-content>
                    <b-icon-three-dots-vertical />
                  </template>

                  <b-dropdown-item @click="createBoard">
                    Create board
                  </b-dropdown-item>

                  <b-dropdown-item v-if="false" @click="syncWithTrello">
                    Sync with Trello
                  </b-dropdown-item>
                </b-dropdown>
              </div>
            </b-nav-text>

            <b-nav-item
              v-for="board of boards"
              :key="board.id"
              :to="'/boards/' + board.id"
              style="margin-left: 2rem;"
            >
              {{ board.title }}
            </b-nav-item>
          </template>
        </b-navbar-nav>

        <b-navbar-nav
          v-if="
            $isAllowed('app/users/list') ||
            $isAllowed('app/invoices/list') ||
            $isAllowed('app/routes/list') ||
            $isAllowed('app/warehouses/list') ||
            $isAllowed('app/companies/list') ||
            $isAllowed('app/jobs/list') ||
            $isAllowed('app/installations/list')
          "
        >
          <b-nav-text>Administrator Menu</b-nav-text>

          <b-nav-item
            v-if="$isAllowed('app/users/list')"
            to="/users"
            class="ml-3"
          >
            Users
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/invoices/list')"
            to="/invoices"
            class="ml-3"
          >
            Invoices
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/routes/list')"
            to="/routes"
            class="ml-3"
          >
            Routes
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/companies/list')"
            to="/companies"
            class="ml-3"
          >
            Companies
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/warehouses/list')"
            to="/warehouses"
            class="ml-3"
          >
            Warehouses
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/jobs/list')"
            to="/exports"
            class="ml-3"
          >
            Exports
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav
          v-if="
            $isAllowed('reports/inventory/count') ||
            $isAllowed('reports/inventory/details') ||
            $isAllowed('reports/inventory/earners') ||
            $isAllowed('reports/inventory/reconcile') ||
            $isAllowed('reports/locations/remains') ||
            $isAllowed('reports/locations/week-to-date') ||
            $isAllowed('reports/locations/all-locations-standard') ||
            $isAllowed('reports/locations/location-standard') ||
            $isAllowed('reports/locations/location-daily') ||
            $isAllowed('reports/locations/location-invoiced') ||
            $isAllowed('reports/locations/device-performance') ||
            $isAllowed('reports/financial/uninvoiced')
          "
        >
          <b-nav-text>Reports</b-nav-text>

          <b-nav-item
            v-if="$isAllowed('reports/locations/week-to-date')"
            to="/reports/week-date"
            class="ml-3"
          >
            Week to Date
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/all-locations-standard')"
            to="/reports/locations-standard"
            class="ml-3"
          >
            All Locations Standard
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/location-standard')"
            to="/reports/location-standard"
            class="ml-3"
          >
            Location Standard
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/location-invoiced')"
            to="/reports/location-invoiced"
            class="ml-3"
          >
            Location Invoiced
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/location-daily')"
            to="/reports/location-daily"
            class="ml-3"
          >
            Location Daily
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/device-performance')"
            to="/reports/device-performance"
            class="ml-3"
          >
            Device Performance
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/inventory/count')"
            to="/reports/inventory-count"
            class="ml-3"
          >
            Inventory Count
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/inventory/details')"
            to="/reports/inventory-details"
            class="ml-3"
          >
            Inventory Details
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/inventory/earners')"
            to="/reports/machine-top-bottom-earners"
            class="ml-3"
          >
            Machine Top Bottom Earners
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/inventory/reconcile')"
            to="/reports/reconcile-by-date"
            class="ml-3"
          >
            Reconcile By Date
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/locations/remains')"
            to="/reports/remains"
            class="ml-3"
          >
            Remains
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/inventory/pos-audit')"
            to="/reports/pos-audit"
            class="ml-3"
          >
            POS Audit
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('reports/financial/uninvoiced')"
            to="/reports/uninvoiced"
            class="ml-3"
          >
            Uninvoiced
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav
          v-if="
            $isAllowed('app/machine-types/list') ||
            $isAllowed('app/game-titles/list') ||
            $isAllowed('app/cabinet-types/list') ||
            $isAllowed('app/padlocks/list') ||
            $isAllowed('app/door-locks/list')
          "
        >
          <b-nav-text>References</b-nav-text>

          <b-nav-item
            v-if="$isAllowed('app/machine-types/list')"
            to="/machine-types"
            class="ml-3"
          >
            Machine Types
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/game-titles/list')"
            to="/game-titles"
            class="ml-3"
          >
            Game Titles
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/software-titles/list')"
            to="/software-titles"
            class="ml-3"
          >
            Software Titles
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/cabinet-types/list')"
            to="/cabinet-types"
            class="ml-3"
          >
            Cabinet Types
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/padlocks/list')"
            to="/padlocks"
            class="ml-3"
          >
            Padlocks
          </b-nav-item>

          <b-nav-item
            v-if="$isAllowed('app/door-locks/list')"
            to="/door-locks"
            class="ml-3"
          >
            Door locks
          </b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <div>
      <aside class="default-layout__sidebar">
        <div class="default-layout__sidebar__menu-header small">
          Menu
        </div>

        <nuxt-link
          to="/"
          class="default-layout__sidebar__menu-item"
          :class="{ 'nuxt-link-exact-active': $route.name === 'index' }"
        >
          <img
            src="~/assets/icons/menu-index.svg"
            width="14"
            height="14"
            class="mr-2"
            alt="Admin Dashboard"
          />
          Admin Dashboard
        </nuxt-link>

        <template v-if="$isAllowed('app/locations/list')">
          <div class="default-layout__sidebar__menu-group-header">
            <img
              src="~/assets/icons/menu-locations.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Locations"
            />
            Locations
          </div>

          <div class="default-layout__sidebar__menu-group">
            <nuxt-link
              to="/locations/all"
              class="default-layout__sidebar__menu-item"
              :class="{
                'nuxt-link-exact-active': $route.name === 'locations-all',
              }"
            >
              All Locations
            </nuxt-link>

            <nuxt-link
              to="/locations/offline"
              class="default-layout__sidebar__menu-item"
              :class="{
                'nuxt-link-exact-active': $route.name === 'locations-offline',
              }"
            >
              Offline Locations
            </nuxt-link>
          </div>
        </template>

        <template v-if="$isAllowed('app/terminals/list')">
          <div class="default-layout__sidebar__menu-group-header">
            <img
              src="~/assets/icons/menu-terminals.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Terminals
          </div>

          <div class="default-layout__sidebar__menu-group">
            <nuxt-link
              to="/terminals/all"
              class="default-layout__sidebar__menu-item"
              :class="{
                'nuxt-link-exact-active': $route.name === 'terminals-all',
              }"
            >
              Machines
            </nuxt-link>

            <nuxt-link
              to="/terminals/offline"
              class="default-layout__sidebar__menu-item"
              :class="{
                'nuxt-link-exact-active': $route.name === 'terminals-offline',
              }"
            >
              Offline Terminals
            </nuxt-link>
          </div>
        </template>

        <template v-if="false && $isAllowed('app/boards/list')">
          <div
            class="default-layout__sidebar__menu-group-header d-flex align-items-center pr-0"
          >
            <img
              src="~/assets/icons/menu-boards.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Boards"
            />
            Boards

            <div class="ml-auto">
              <b-dropdown
                boundary="window"
                size="sm"
                variant="link"
                class="nav-dropdown"
                toggle-class="text-decoration-none"
                dropleft
                no-caret
              >
                <template #button-content>
                  <b-icon-three-dots-vertical />
                </template>

                <b-dropdown-item @click="createBoard">
                  Create board
                </b-dropdown-item>

                <b-dropdown-item v-if="false" @click="syncWithTrello">
                  Sync with Trello
                </b-dropdown-item>
              </b-dropdown>
            </div>
          </div>

          <div class="default-layout__sidebar__menu-group">
            <nuxt-link
              v-for="board of boards"
              :key="board.id"
              :to="'/boards/' + board.id"
              :class="{
                'nuxt-link-exact-active': $route.name === '/boards/' + board.id,
              }"
              class="default-layout__sidebar__menu-item"
            >
              {{ board.title }}
            </nuxt-link>
          </div>
        </template>

        <template
          v-if="
            $isAllowed('app/users/list') ||
            $isAllowed('app/invoices/list') ||
            $isAllowed('app/routes/list') ||
            $isAllowed('app/warehouses/list') ||
            $isAllowed('app/companies/list') ||
            $isAllowed('app/jobs/list') ||
            $isAllowed('app/installations/list')
          "
        >
          <div class="default-layout__sidebar__menu-header small">
            Administrator Menu
          </div>

          <nuxt-link
            v-if="$isAllowed('app/users/list')"
            to="/users"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'users' }"
          >
            <img
              src="~/assets/icons/menu-users.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Users"
            />
            Users
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/invoices/list')"
            to="/invoices"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'invoices' }"
          >
            <img
              src="~/assets/icons/menu-invoices.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Invoices"
            />
            Invoices
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/routes/list')"
            to="/routes"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'routes' }"
          >
            <img
              src="~/assets/icons/menu-routes.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Routes"
            />
            Routes
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/companies/list')"
            to="/companies"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'companies' }"
          >
            <img
              src="~/assets/icons/menu-companies.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Companies"
            />
            Companies
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/warehouses/list')"
            to="/warehouses"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'warehouses' }"
          >
            <img
              src="~/assets/icons/menu-warehouses.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Warehouses"
            />
            Warehouses
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/jobs/list')"
            to="/exports"
            class="default-layout__sidebar__menu-item"
            :class="{ 'nuxt-link-exact-active': $route.name === 'exports' }"
          >
            <img
              src="~/assets/icons/menu-exports.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Exports"
            />
            Exports
          </nuxt-link>
        </template>

        <template
          v-if="
            $isAllowed('reports/inventory/count') ||
            $isAllowed('reports/inventory/details') ||
            $isAllowed('reports/inventory/earners') ||
            $isAllowed('reports/inventory/reconcile') ||
            $isAllowed('reports/locations/remains') ||
            $isAllowed('reports/locations/week-to-date') ||
            $isAllowed('reports/locations/all-locations-standard') ||
            $isAllowed('reports/locations/location-standard') ||
            $isAllowed('reports/locations/location-daily') ||
            $isAllowed('reports/locations/location-invoiced') ||
            $isAllowed('reports/locations/device-performance') ||
            $isAllowed('reports/financial/uninvoiced')
          "
        >
          <div class="default-layout__sidebar__menu-header small">
            Reports
          </div>

          <nuxt-link
            v-if="$isAllowed('reports/locations/week-to-date')"
            to="/reports/week-date"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'reports-week-date',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Week to Date
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/all-locations-standard')"
            to="/reports/locations-standard"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-locations-standard',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="All Locations Standard"
            />
            All Locations Standard
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/location-standard')"
            to="/reports/location-standard"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-location-standard',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Location Standard"
            />
            Location Standard
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/location-invoiced')"
            to="/reports/location-invoiced"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-location-invoiced',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Location Invoiced"
            />
            Location Invoiced
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/location-daily')"
            to="/reports/location-daily"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-location-daily',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Location Daily"
            />
            Location Daily
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/device-performance')"
            to="/reports/device-performance"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-device-performance',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Device Performance"
            />
            Device Performance
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/inventory/count')"
            to="/reports/inventory-count"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-inventory-count',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Inventory Count"
            />
            Inventory Count
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/inventory/details')"
            to="/reports/inventory-details"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-inventory-details',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Inventory Details"
            />
            Inventory Details
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/inventory/earners')"
            to="/reports/machine-top-bottom-earners"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-machine-top-bottom-earners',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Machine Top Bottom Earners"
            />
            Machine Top Bottom Earners
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/inventory/reconcile')"
            to="/reports/reconcile-by-date"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active':
                $route.name === 'reports-reconcile-by-date',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Reconcile By Date"
            />
            Reconcile By Date
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/locations/remains')"
            to="/reports/remains"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'reports-remains',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Reconcile By Date"
            />
            Remains
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/inventory/pos-audit')"
            to="/reports/pos-audit"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'pos-audit',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="POS Audit"
            />
            POS Audit
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('reports/financial/uninvoiced')"
            to="/reports/uninvoiced"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'reports-uninvoiced',
            }"
          >
            <img
              src="~/assets/icons/menu-report.svg"
              width="14"
              height="14"
              class="mr-2"
              alt="Reconcile By Date"
            />
            Uninvoiced
          </nuxt-link>
        </template>

        <template
          v-if="
            $isAllowed('app/machine-types/list') ||
            $isAllowed('app/game-titles/list') ||
            $isAllowed('app/software-titles/list') ||
            $isAllowed('app/cabinet-types/list') ||
            $isAllowed('app/padlocks/list') ||
            $isAllowed('app/door-locks/list')
          "
        >
          <div class="default-layout__sidebar__menu-header small">
            References
          </div>
          <nuxt-link
            v-if="$isAllowed('app/machine-types/list')"
            to="/machine-types"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'machine-types',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Machine Types
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/game-titles/list')"
            to="/game-titles"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'game-titles',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Game Titles
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/software-titles/list')"
            to="/software-titles"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'software-titles',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Software Titles
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/cabinet-types/list')"
            to="/cabinet-types"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'cabinet-types',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Cabinet Types
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/padlocks/list')"
            to="/padlocks"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'padlocks',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Padlocks
          </nuxt-link>

          <nuxt-link
            v-if="$isAllowed('app/door-locks/list')"
            to="/door-locks"
            class="default-layout__sidebar__menu-item"
            :class="{
              'nuxt-link-exact-active': $route.name === 'door-locks',
            }"
          >
            <img
              src="~/assets/icons/menu-dictionary.svg"
              width="14"
              height="14"
              class="mr-2"
              alt=""
            />
            Door locks
          </nuxt-link>
        </template>
      </aside>

      <main class="default-layout__content">
        <Nuxt />
      </main>
    </div>

    <BoardModal ref="board-modal" @updated="getBoards" />
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import CompanySelector from '@/components/selectors/CompanySelector'
import { BIconThreeDotsVertical } from 'bootstrap-vue'
import BoardModal from '@/components/modals/Board'

export default {
  components: {
    CompanySelector,
    BIconThreeDotsVertical,
    BoardModal,
  },
  middleware({ store, redirect }) {
    if (store.getters['user/user'] && !store.getters['user/user'].permissions) {
      store.dispatch('user/logout')
    }

    if (!store.getters['user/token']) {
      return redirect('/sign-in')
    }
  },
  data() {
    return {
      isVisible: false,
    }
  },
  computed: {
    ...mapGetters({
      user: 'user/user',
      installations: 'stock/installations',
      boards: 'boards/boards',
    }),
  },
  watch: {
    $route() {
      this.isVisible = false
    },
  },
  created() {
    // this.getBoards()
  },
  methods: {
    ...mapActions({
      getBoards: 'boards/getBoards',
      logout: 'user/logout',
    }),
    confirmLogout() {
      this.$bvModal
        .msgBoxConfirm('Are you sure you want to logout?', {
          title: 'Logout confirmation',
          headerBgVariant: 'primary',
          centered: true,
          okTitle: 'Logout',
        })
        .then((value) => {
          if (value) {
            this.logout()
          }
        })
        .catch((error) => console.error(error))
    },
    createBoard() {
      this.$refs['board-modal'].showModal(null)
    },
    syncWithTrello() {},
  },
}
</script>

<style lang="scss">
.default-layout {
  position: relative;

  &__logo {
    padding: 0;
    font-size: 2rem;
    font-weight: bold;
  }

  &__sidebar {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 17rem;
    padding: 5rem 1rem 1rem 1rem;
    background-color: #0e0c28;
    color: #a4aadb;
    overflow-y: auto;
    z-index: 1019;

    @media (min-width: 1200px) {
      display: block;
    }

    a {
      color: #7a80b4;
      text-decoration: none;
    }

    &__menu-header {
      padding: 1rem 0.75rem;
      text-transform: uppercase;
      font-weight: bold;
      letter-spacing: 1px;
    }

    &__menu-group-header {
      display: flex;
      align-items: center;
      padding: 0.75rem;
      border-radius: 0.25rem;
    }

    &__menu-group {
      padding-left: 1.5rem;
    }

    &__menu-item {
      display: flex;
      align-items: center;
      padding: 0.75rem;
      border-radius: 0.25rem;

      &:hover,
      &:focus,
      &.nuxt-link-exact-active {
        background-color: #242849;
        color: #fff;
      }
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 4rem);
    padding: 1.5rem;

    @media (min-width: 1200px) {
      padding: 1.5rem 1.5rem 1.5rem 18.5rem;
    }
  }

  .nav-dropdown {
    button {
      color: #7a80b4;

      &:hover {
        color: #4f5376;
      }
    }
  }
}

.mobile-menu {
  display: none !important;
}

.mobile-menu_open {
  position: fixed;
  top: 56px;
  right: 0;
  bottom: 0;
  left: 0;
  display: block !important;
  background-color: #fff;
  padding: 12px 24px;
  overflow-y: auto;
}
</style>
